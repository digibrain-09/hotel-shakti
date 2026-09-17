<?php

namespace App\Http\Controllers;

use App\Models\KOT;
use App\Models\Restaurant;
use App\Models\RestaurantAdmin;
use App\Models\User;
use App\Models\Customer;
use App\Models\Queue;
use App\Models\Order;
use App\Models\OrderPrint;
use App\Models\Table;
use App\Models\RestaurantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\Item;

use Illuminate\Support\Facades\Cookie;
use App\Jobs\Jobname–queued;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;

use App\Services\PushNotificationService;


require_once app_path('dompdf/autoload.inc.php');
require_once app_path('Helpers/BusinessDayHelper.php');

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

use Carbon\Carbon;

class WaiterController extends Controller
{
    protected PushNotificationService $push;

    public function __construct(PushNotificationService $push)
    {
        $this->push = $push;
    }
    public function index()
    {
        $show_session = session()->get('session_code');
        $restaurant_id = session()->get('restaurant_id');

        $restaurant = Restaurant::where('id', $restaurant_id)->get();

        $tableCount = Table::where('restaurant_id', $restaurant_id)->count();

        $tableAirCount = Table::where('restaurant_id', $restaurant_id)->where('seating_type', true)->count();

        $tableNonAirCount = Table::where('restaurant_id', $restaurant_id)->where('seating_type', false)->count();

        $availableTableCount = DB::table('restaurant_tables')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('customers')
                    ->join('orders', 'orders.customer_code', '=', 'customers.customer_code')
                    ->whereColumn('customers.table_id', 'restaurant_tables.id')
                    ->whereIn('orders.status', ['processed']); // adjust statuses
            })
            ->count();


        $subquery = DB::table('customers')
            ->selectRaw('table_id, COUNT(*) as total_customers')
            ->groupBy('table_id');

        $tables = DB::table('restaurant_tables')
            ->leftJoinSub($subquery, 'customer_counts', function ($join) {
                $join->on('restaurant_tables.id', '=', 'customer_counts.table_id');
            })
            ->leftJoin('orders', function ($join) {
                $join->on('orders.customer_code', '=', DB::raw("(SELECT customer_code FROM customers WHERE customers.table_id = restaurant_tables.id LIMIT 1)"));
            })
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('restaurant_tables.*', DB::raw('COALESCE(customer_counts.total_customers, 0) as count'), 'orders.status')
            // ->groupBy('restaurant_tables.id', 'orders.status')  // Prevent duplicates
            ->orderBy('restaurant_tables.id', 'ASC')
            ->get();


        $details = Queue::all();

        foreach ($details as $detail) {
            $payload = json_decode($detail->payload);
            $obj = unserialize($payload->data->command);

            if (property_exists($obj, 'details')) {
                $code = $obj->details;
                $customerCode = $code['customer_code'] ?? null;
                // process customerCode
            } else {
                // skip old job or handle default
                continue;
            }
        }


        $customer = DB::table('customers')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('customers.customer_code', $customerCode)
            ->selectRaw('restaurant_tables.table_name')
            ->get();

        return view('waiter.index', compact('tables', 'customer', 'restaurant', 'tableCount', 'tableAirCount', 'tableNonAirCount', 'availableTableCount'));
    }

    public function getMenu($id)
    {
        $restaurant_id = session()->get('restaurant_id');
        session()->put('tableId', $id);

        $table_id = $id;

        if (!$restaurant_id || !$table_id) {
            return redirect()->route('home')->with('msg', 'Invalid session data.');
        }

        $categories = Category::where('restaurant_id', $restaurant_id)->get();
        $customers = DB::table('orders')->latest('created_at')->first();
        $cart = Cart::where('table_id', $table_id)->count();
        $catTab = $categories->first()->id ?? null;

        $cart_items = Cart::where('table_id', $table_id)->get();

        $table_name = Table::where('id', $table_id)->value('table_name');


        return view('waiter.category', compact('categories', 'catTab', 'cart', 'customers', 'table_id', 'cart_items', 'table_name'));
        // return view('waiter.menu');

    }

    public function getItems($table_id, $category_id)
    {
        $tableId = $table_id;
        $items = Item::where('category_id', $category_id)->get();
        $cart = count((is_countable(session()->get('cart')) ? session()->get('cart') : []));
        $cart = Cart::where('table_id', $table_id)->count();


        $cart_items = Cart::where('table_id', $tableId)->get();

        $table_name = Table::where('id', $tableId)->value('table_name');

        $seating_type = Table::where('id', $tableId)->value('seating_type');
        session()->put('seating_type', $seating_type);

        $cartItems = Cart::where('table_id', $tableId)->pluck('quantity', 'item_id')->toArray();

        $add_ons = DB::table('item_addons')
            ->get(['item_addons.*']);


        return view('waiter.items', compact('items', 'cart', 'tableId', 'cart_items', 'table_name', 'cartItems', 'add_ons', 'seating_type'));
        // return view('waiter.menu');
    }

    public function view($id)
    {

        $seating_type = session()->get('seating_type');
        $items = DB::table('items')
            ->leftJoin('item_addons', 'items.id', '=', 'item_addons.item_id')
            ->leftJoin('add_ons', 'add_ons.id', '=', 'item_addons.addon_id')
            ->where('items.id', $id)
            ->select(
                'items.id as item_id',
                'items.item_name as item_name',
                'items.price as item_price',
                'items.ac_price as item_ac_price',
                'items.description as description',
                'items.food_type as food_type',
                'items.picture',
                'add_ons.id as addon_id',
                'add_ons.name as addon_name',
                'add_ons.price as addon_price',
                'add_ons.type as addon_type'
            )
            ->get();

        if ($items->isEmpty()) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $first = $items->first();

        // Filter out null add-on rows (when no match exists)
        $addons = $items->filter(function ($row) {
            return $row->addon_id !== null;
        })->map(function ($row) {
            return [
                'id' => $row->addon_id,
                'name' => $row->addon_name,
                'price' => $row->addon_price,
                'type' => $row->addon_type,
            ];
        })->values(); // reset array keys

        if ($seating_type == 1) {
            return response()->json([
                'item_id' => $first->item_id,
                'name' => $first->item_name,
                'picture' => $first->picture,
                'price' => $first->item_ac_price,
                'description' => $first->description,
                'food_type' => $first->food_type,
                'add_ons' => $addons,
            ]);
        } else {
            return response()->json([
                'item_id' => $first->item_id,
                'name' => $first->item_name,
                'picture' => $first->picture,
                'price' => $first->item_price,
                'description' => $first->description,
                'food_type' => $first->food_type,
                'add_ons' => $addons,
            ]);
        }
    }

    public function cart_view($id)
    {
        $table_id = session()->get('tableId');
        $seating_type = session()->get('seating_type');

        // Get cart data for this item and table
        $cart = DB::table('carts')
            ->where('item_id', $id)
            ->where('table_id', $table_id)
            ->first();

        if (!$cart) {
            return response()->json(['error' => 'Cart item not found'], 404);
        }

        // Get item + possible addons
        $items = DB::table('items')
            ->leftJoin('item_addons', 'items.id', '=', 'item_addons.item_id')
            ->leftJoin('add_ons', 'add_ons.id', '=', 'item_addons.addon_id')
            ->where('items.id', $id)
            ->select(
                'items.id as item_id',
                'items.item_name as item_name',
                'items.price as item_price',
                'items.ac_price as item_ac_price',
                'items.description as description',
                'items.food_type as food_type',
                'items.picture',
                'add_ons.id as addon_id',
                'add_ons.name as addon_name',
                'add_ons.price as addon_price',
                'add_ons.type as addon_type'
            )
            ->get();

        if ($items->isEmpty()) {
            return response()->json(['error' => 'Item not found'], 404);
        }

        $first = $items->first();

        // Decode selected add-ons from cart
        $selectedAddons = json_decode($cart->addons ?? '[]', true); // expect array of IDs or objects

        $addons = $items->filter(function ($row) {
            return $row->addon_id !== null;
        })->map(function ($row) use ($selectedAddons) {
            return [
                'id' => $row->addon_id,
                'name' => $row->addon_name,
                'price' => $row->addon_price,
                'type' => $row->addon_type,
                'selected' => collect($selectedAddons)->contains(function ($sel) use ($row) {
                    return is_array($sel) ? $sel['id'] == $row->addon_id : $sel == $row->addon_id;
                }),
            ];
        })->values();

        if ($seating_type == 1) {
            return response()->json([
                'item_id' => $first->item_id,
                'name' => $first->item_name,
                'picture' => $first->picture,
                'price' => $first->item_ac_price,
                'description' => $first->description,
                'food_type' => $first->food_type,
                'add_ons' => $addons,
                'note' => $cart->note,
                'quantity' => $cart->quantity,
            ]);
        } else {
            return response()->json([
                'item_id' => $first->item_id,
                'name' => $first->item_name,
                'picture' => $first->picture,
                'price' => $first->item_price,
                'description' => $first->description,
                'food_type' => $first->food_type,
                'add_ons' => $addons,
                'note' => $cart->note,
                'quantity' => $cart->quantity,
            ]);
        }
    }


    public function AddtoCart(Request $request)
    {

        $table_id = session()->get('tableId');

        $itemId = $request->input('item_id');
        $quantity = $request->input('quantity');
        $addons = $request->input('addons'); // array
        $note = $request->input('note');
        $item_name = $request->input('item_name');
        $price = $request->input('price'); // array
        $item_image = $request->input('item_image');

        // Logic to add to cart (session/database/etc.)
        // Cart::add($itemId, $quantity, $addons, $note);

        $cart = new Cart();
        $cart->item_id = $itemId;
        $cart->table_id = $table_id;
        $cart->name = $item_name;
        $cart->price = $price;
        $cart->quantity = $quantity;
        $cart->addons = json_encode($addons);;
        $cart->note = $note;
        $cart->image = $item_image;
        $cart->save();

        return response()->json(['success' => true]);
    }

    // public function GotoCart()
    // {

    //     $table_id = session()->get('table_id');
    //     $items = Item::all();
    //     // $show_session = session()->get('cart');
    //     $cart = Cart::where('table_id', $table_id)->get();
    //     // return $show_session;
    //     return view('waiter.cart', compact('items', 'cart'));
    // }

    public function RemovefromCart($id)
    {
        $cart = Cart::find($id);
        $cart->delete();
        return redirect()->back();
    }

    public function UpdateQuantity(Request $request)
    {
        if ($request->id) {
            $cart = Cart::find($request->id);
            $cart->quantity = $request->quantity;
            $cart->update();
        }
    }

    public function update(Request $request)
    {

        $table_id = session()->get('tableId');
        $item_id = $request->input('item_id');
        $action = $request->input('action');

        $cartItem = Cart::where('table_id', $table_id)->where('item_id', $item_id)->first();

        if (!$cartItem) {
            return response()->json(['status' => 'error', 'message' => 'Item not found in cart.'], 404);
        }

        if ($action === 'increment') {
            $cartItem->quantity += 1;
        } elseif ($action === 'decrement' && $cartItem->quantity > 1) {
            $cartItem->quantity -= 1;
        }

        if ($action === 'update_cart') {

            $cartItem->addons = $request->input('addons');
            $cartItem->quantity = $request->input('quantity');
            $cartItem->note = $request->input('note');
        }

        $cartItem->update();

        return response()->json([
            'status' => 'success',
            'message' => 'Cart updated successfully.',
            'quantity' => $cartItem->quantity
        ]);
    }

    public function Order(Request $request)
    {

        $session_code = Cookie::get('CustomerOrderId');

        $table_id = session()->get('tableId');
        // $existingCustomer = Customer::where('table_id', $table_id)
        //     ->first();

        $existingCustomer = DB::table('customers')
            ->join('orders', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('customers.table_id', $table_id)
            ->where('orders.status', 'processed') // ✅ Only use customer with active (not completed) orders
            ->select('customers.*')
            ->first();

        // return $existingCustomer;

        $restaurant_id = session()->get('restaurant_id');
        $table_name = session()->get('table_name');

        // Check if an existing customer is found
        if ($existingCustomer) {
            $customer_code = $existingCustomer->customer_code;
        } else {
            // If no customer found, create a new customer and assign customer_code
            $code = "CUST" . rand(10000, 99999999);
            $minutes = 5 * 60;
            Cookie::queue('CustomerOrderId', $code, $minutes);

            // Create a new customer
            $customer = new Customer();
            $customer->customer_code = $code;
            $customer->table_id = $table_id;
            $customer->restaurant_id = $restaurant_id;
            $customer->phone = $request->phone;
            $customer->save();

            $customer_code = $code;  // Set customer_code for new customer

            session()->put('customer_code', $customer_code);
        }

        // Fetch the cart items
        $cart = Cart::where('table_id', $table_id)->get();
        $status = 'processed';
        $kitchen_order_status = 'sent_to_kitchen';
        $is_notified = 'true';
        $order_confirm_by = "waiter";
        $order_type = 'Dine In';
        $total_amount = $request->total_amount;



        // Check if there are existing orders for the customer
        $orders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('customers.table_id', $table_id)
            ->where('orders.status', 'processed')
            ->select('orders.*')
            ->get();



        // return $orders;

        $order_print = DB::table('order_print')
            // ->where('order_print.customer_code', $session_code)
            ->join('orders', 'orders.customer_code', '=', 'order_print.customer_code')
            ->where('order_print.table_id', $table_id)
            ->where('orders.status', 'processed')
            ->selectRaw('order_print.*')
            ->get();

        $items = DB::table('items')
            ->where('restaurant_id', $restaurant_id)
            ->get()
            ->keyBy('id');

        if ($orders->isEmpty()) {
            // If no existing orders, create a new order
            $json_data = [];
            $order = new Order();
            $order->customer_code = $customer_code; // Use the customer_code
            $order->order_type = $order_type;

            foreach ($cart as $number => $val) {

                $addons = json_decode($val['addons'], true);
                $menuItem = $items->get($val['item_id']);

                $json_data[] = [
                    'item_id' => $val['item_id'],
                    "name" => $val['name'],
                    'price' => $val['price'],
                    'quantity' => $val['quantity'],
                    'addons' => json_encode($addons),
                    'note' => $val['note'],
                    'fulfillment_type' => $menuItem->fulfillment_type ?? 'kitchen',
                    'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                ];

                $json_data_print[] = [
                    'item_id' => $val['item_id'],
                    "name" => $val['name'],
                    'price' => $val['price'],
                    'quantity' => $val['quantity'],
                    'addons' => json_encode($addons),
                    'note' => $val['note'],
                    'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                    'print' => 'No',
                    'ReadytoServed' => 'No',
                    'fulfillment_type' => $menuItem->fulfillment_type ?? 'kitchen',
                    "alerted" => "No"
                ];
            }


            $data1 = json_encode($json_data);
            $order->data = $data1;
            $order->status = $status;
            $order->kitchen_order_status = $kitchen_order_status;
            $order->is_notified = $is_notified;
            $order->total_amount = $total_amount;
            $order->order_confirm_by = $order_confirm_by;
            $order->save();

            $order_id = $order->id;

            $orderprint = new OrderPrint();
            $orderprint->customer_code = $customer_code;

            $item_data = json_encode($json_data_print);

            $orderprint->item_data = $item_data;
            $orderprint->order_id = $order->id;
            $orderprint->table_id = $table_id;
            $orderprint->save();

            // return view('client.KOT', compact('invoiceData'));
        } else {
            // If there are existing orders, update the order data
            foreach ($orders as $order) {
                // if ($order->customer_code == $session_code) {
                $order_id = $order->id;
                $json = json_decode($order->data);

                foreach ($cart as $number => $val) {

                    $addons = json_decode($val['addons'], true);
                    $menuItem = $items->get($val['item_id']);

                    $json[] = [
                        'item_id' => $val['item_id'],
                        "name" => $val['name'],
                        'price' => $val['price'],
                        'quantity' => $val['quantity'],
                        'addons' => json_encode($addons),
                        'note' => $val['note'],
                        'fulfillment_type' => $menuItem->fulfillment_type ?? 'kitchen',
                        'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                    ];
                }


                $data1 = json_encode($json);


                $total_amount = $order->total_amount + $total_amount;

                // Update the order data
                Order::where('id', $order->id)
                    ->update(['data' => $data1, 'is_notified' => $is_notified, 'total_amount' => $total_amount, 'order_confirm' => false, 'order_notified' => false, 'kitchen_order_status' => $kitchen_order_status]);

                foreach ($order_print as $item) {

                    $json_data_print = json_decode($item->item_data);
                    $menuItem = $items->get($val['item_id']);

                    foreach ($cart as $number => $val) {
                        $json_data_print[] = [
                            'item_id' => $val['item_id'],
                            "name" => $val['name'],
                            'price' => $val['price'],
                            'quantity' => $val['quantity'],
                            'addons' => json_encode($addons),
                            'note' => $val['note'],
                            'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                            'print' => 'No',
                            'ReadytoServed' => 'No',
                            'fulfillment_type' => $menuItem->fulfillment_type ?? 'kitchen',
                            "alerted" => "No"
                        ];
                    }

                    $data2 = json_encode($json_data_print);

                    OrderPrint::where('id', $item->id)
                        ->update(['item_data' => $data2, 'status' => 0]);
                }



                // return view('client.KOT', compact('line_items'));
                // }
            }
        }

        $this->push->send(
            ['kitchen_owner'],
            'New Order',
            'Kitchen, New order ' . $customer_code . ' at ' . $table_name . '.'
        );

        $this->push->send(
            ['restaurant_admin'],
            'New Order',
            'Admin, New order ' . $customer_code . ' at ' . $table_name . '.'
        );

        // Dispatch jobs for each cart item
        foreach ($cart as $number => $val) {
            $details['customer_code'] = $customer_code;  // Ensure the correct customer code is used
            $details['item_id'] = $val['item_id'];
            $details['quantity'] = $val['quantity'];

            Jobname–queued::dispatch($details)->delay(now()->addYear());
        }

        // Delete cart items after order is processed
        $ids = explode(",", $table_id);
        Cart::whereIn('table_id', $ids)->delete();

        // return redirect()->route('home')->with('success', 'Order placed successfully!');
        return response()->json([
            'success' => true,
            'order_id' => $order_id
        ]);
    }

    public function PrintMyOrder($id)
    {
        $restaurant_id = session()->get('restaurant_id');

        $orders = DB::table('order_print')
            ->where('order_id', $id)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found'], 404);
        }

        foreach ($orders as $order) {
            $tableId = $order->table_id;
            // Decode JSON to array
            $item_data = json_decode($order->item_data, true);
            // Create a copy for printing that still has items marked "No"
            $item_data_for_print = $item_data;
            $date = $order->updated_at;

            $customer_code = $order->customer_code;

            // Update print status to "Yes" for each item
            foreach ($item_data as &$item) {
                $item['print'] = 'Yes';
            }

            // Encode back to JSON
            $updated_item_data = json_encode($item_data);

            // Update the order in the database
            DB::table('order_print')
                ->where('order_id', $id)
                ->update([
                    'item_data' => $updated_item_data,
                    'status' => 1
                ]);

            Order::where('id', $id)->update(['order_confirm' => true, 'kitchen_order_notified' => false, 'manager_kitchen_order_notified' => true, 'waiter_kitchen_order_notified' => false]);
        }

        return response()->json([
            'success' => true,
            'message' => 'You can print now'
        ]);
    }

    public function CheckCondition()
    {
        // $details = Queue::all();
        // foreach ($details as $detail) {
        //     $payload = json_decode($detail->payload);
        //     $obj = unserialize($payload->data->command);
        //     $code = $obj->details;
        //     $customerCode = $code['customer_code'];
        // }

        $customer = DB::table('customers')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('orders', 'orders.customer_code', '=', 'customers.customer_code')
            ->where('orders.status', 'processed')
            ->selectRaw('restaurant_tables.table_name')
            ->get();

        // return $customer;

        // Your logic to determine the condition status
        foreach ($customer as $row) {
            $condition = true;
            $matchValue = $row->table_name;
        }

        // Return the condition status as JSON
        return response()->json(['condition' => $condition, 'customer' => $customer]);
    }

    public function OrderDetails(Request $request)
    {

        $show_session = session()->get('session_code');
        $restaurant_id = session()->get('restaurant_id');

        $restaurant = Restaurant::where('id', $restaurant_id)->get();

        $tableCount = Table::where('restaurant_id', $restaurant_id)->count();

        $tableAirCount = Table::where('restaurant_id', $restaurant_id)->where('seating_type', true)->count();

        $tableNonAirCount = Table::where('restaurant_id', $restaurant_id)->where('seating_type', false)->count();

        $availableTableCount = DB::table('restaurant_tables')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('customers')
                    ->join('orders', 'orders.customer_code', '=', 'customers.customer_code')
                    ->whereColumn('customers.table_id', 'restaurant_tables.id')
                    ->whereIn('orders.status', ['processed']); // adjust statuses
            })
            ->count();


        $subquery = DB::table('customers')
            ->selectRaw('table_id, COUNT(*) as total_customers')
            ->groupBy('table_id');

        $tables = DB::table('restaurant_tables')
            ->leftJoinSub($subquery, 'customer_counts', function ($join) {
                $join->on('restaurant_tables.id', '=', 'customer_counts.table_id');
            })
            ->leftJoin('orders', function ($join) {
                $join->on('orders.customer_code', '=', DB::raw("(SELECT customer_code FROM customers WHERE customers.table_id = restaurant_tables.id LIMIT 1)"));
            })
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('restaurant_tables.*', DB::raw('COALESCE(customer_counts.total_customers, 0) as count'), 'orders.status')
            // ->groupBy('restaurant_tables.id', 'orders.status')  // Prevent duplicates
            ->orderBy('restaurant_tables.id', 'ASC')
            ->get();


        $details = Queue::all();

        foreach ($details as $detail) {
            $payload = json_decode($detail->payload);
            $obj = unserialize($payload->data->command);

            if (property_exists($obj, 'details')) {
                $code = $obj->details;
                $customerCode = $code['customer_code'] ?? null;
                // process customerCode
            } else {
                // skip old job or handle default
                continue;
            }
        }


        $customer = DB::table('customers')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('customers.customer_code', $customerCode)
            ->selectRaw('restaurant_tables.table_name')
            ->get();

        return view('waiter.order_details', compact('tables', 'customer', 'restaurant', 'tableCount', 'tableAirCount', 'tableNonAirCount', 'availableTableCount'));
    }

    public function getOrderDetails(Request $request)
    {
        $checkout_with_tax = 0;
        $cgst = 0;
        $sgst = 0;
        $cgst_rate = 0;
        $sgst_rate = 0;
        $total_with_tax = 0;
        $GST_status = 0;
        $total = 0;
        // $session_code = Cookie::get('CustomerOrderId');

        $invoice_data = '';
        $table_id = $request->table_id;

        [$start, $end] = getBusinessTimeRange();


        $restaurant_id = session()->get('restaurant_id');

        $orderdata = DB::table('orders')
            ->selectRaw('orders.*')
            ->get();

        // Get the latest orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(order_confirm_by) as order_confirm_by')
            ->groupBy('customer_code');


        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('order_print', 'order_print.customer_code', '=', 'order_counts.customer_code')
            // ->join('invoice_data', 'invoice_data.order_id', '=', 'order_counts.id')
            ->where('customers.table_id', $request->table_id)
            // ->whereDate('order_counts.latest_created_at', '=', today())
            ->whereDate('order_counts.latest_created_at', '=', [$start, $end])
            ->select('customers.customer_code', 'order_counts.count', 'order_counts.status', 'order_counts.latest_created_at', 'order_counts.latest_updated_at', 'order_counts.id', 'order_print.status as order_print_status', 'order_counts.order_confirm_by')
            ->orderBy('order_counts.latest_created_at', 'DESC')
            ->orderBy('order_counts.status', 'DESC')
            ->paginate(10)
            ->appends(request()->query()); // Add this line to maintain query parameters
        // return $orders;

        foreach ($orders as $order) {
            $order_id = $order->id;
            $session_code = $order->customer_code;
        }



        $data = DB::table('orders')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->selectRaw('orders.*, order_print.item_data')
            ->get();


        $order_print = DB::table('order_print')
            ->selectRaw('order_print.*')
            ->get();


        $orders2 = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('customers.table_id', $table_id)
            ->where('orders.status', 'processed')
            ->get(['orders.*', 'customers.restaurant_id']);



        foreach ($orders2 as $order) {
            // $data = json_decode($order->data);
            $orderId = $order->id;
            $checkout_with_tax  = $order->checkout_with_tax;
            $restaurant_id = $order->restaurant_id;
            $total_amount = $order->total_amount;
            $order->items = json_decode($order->data);

            if (isset($restaurant_id)) {
                $restaurant = DB::table('restaurants')->where('id', $restaurant_id)->first();

                $cgst_rate = $restaurant->CGST;
                $sgst_rate = $restaurant->SGST;
                $GST_status = $restaurant->GST_status;

                $total = $total_amount;
                $cgst = ($total * $cgst_rate) / 100;
                $sgst = ($total * $sgst_rate) / 100;
                $total_with_tax = $total + $cgst + $sgst;
            }
        }



        // return $orders2;

        if (isset($order_id)) {
            $invoice_data = DB::table('invoice_data')
                ->where('table_id', $table_id)
                ->get();
        }

        $last = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('orders.customer_code')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->first();


        return view('waiter.get_order_details', compact('orders', 'data', 'table_id', 'orders2', 'order_print', 'checkout_with_tax', 'cgst_rate', 'sgst_rate', 'cgst', 'sgst', 'total_with_tax', 'invoice_data', 'GST_status', 'total', 'last'));
    }

    public function CompleteOrder(Request $request)
    {

        $line_items = [];
        $payment_method = 'Admin_' . uniqid();
        // $payment_mode = 'cash';

        foreach ($request->product_name as $index => $product_name) {
            $addons = json_decode($request->addons[$index], true); // decode back to array

            $line_items[] = [
                'name' => $product_name,
                'price' => $request->product_price[$index],
                'quantity' => $request->product_quantity[$index],
                'addons' => $addons, // Now correctly handled
            ];
        }

        $customer_code = $request->customer_code;
        $orderId = $request->order_id;
        $existingCustomer = Order::where('id', $orderId)->where('customer_code', $customer_code)->first();

        $status = 'completed';

        $restaurant_name = session()->get('restaurant_name');
        $restaurant_id = session()->get('restaurant_id');
        // $table_id = session()->get('tableId');
        $orderId = $request->order_id;

        $table_name = Table::where('id', $request->table_id)->value('table_name');

        $restaurant = DB::table('restaurants')->where('id', session('restaurant_id'))->first();

        $order = Order::findOrFail($request->order_id);

        if (!empty($line_items)) {

            // Generate PDF invoice
            $invoiceData = [
                'table' => $table_name,
                'date' => now()->format('d-m-Y'),
                'time' => date("h:i:sa"),
                'item_data' => $line_items,
                'cgst_rate' => $request->cgst_rate,
                'sgst_rate' => $request->sgst_rate,
                'cgst' => $request->cgst,
                'sgst' => $request->sgst,
                'total_with_tax' => $request->total_with_tax,
                'coupon_code' => $request->coupon,
                'discount' => $request->discount,
                // 'payment_mode' => $payment_mode,
            ];

            $invoice_json = json_encode($invoiceData);

            $invoice = new Invoice();
            $invoice->invoice_url = $payment_method;
            $invoice->invoicedata = $invoice_json;
            $invoice->restaurant_name = $restaurant_name;
            $invoice->restaurant_id = $restaurant_id;
            $invoice->table_name = $table_name;
            $invoice->table_id = $request->table_id;
            $invoice->customer_code = $customer_code;
            $invoice->order_id = $orderId;

            // $invoice->payment_mode = $payment_mode;
            $invoice->save();

            DB::table('orders')->where('id', $orderId)->update([
                'cgst' => $request->cgst,
                'sgst' => $request->sgst,
                'taxable_amount' => $request->total_with_tax,
                'coupon_code' => $request->coupon,
                'discount' => $request->discount,
            ]);

            $html = View::make('client.invoice', ['order' => $order,'restaurant' => $restaurant, 'invoiceData' => $invoiceData])->render();

            // Set Dompdf options
            $options = new Options();
            $options->set('defaultFont', 'Helvetica');

            // Initialize Dompdf
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save the PDF file in storage
            $pdfPath = public_path('invoices/invoice_' . $payment_method . '.pdf');
            file_put_contents($pdfPath, $dompdf->output());

            if ($existingCustomer) {
                $existingCustomer->status = $status;
                $existingCustomer->update();
            }
            Cookie::queue(Cookie::forget('CustomerOrderId'));

            session()->forget(['cgst', 'sgst', 'total_with_tax', 'coupon', 'discount']);

            $currentOrder = $existingCustomer; // your saved order

            $this->push->send(
                ['restaurant_admin'],
                'Payment Mode',
                'Admin, You need to set the payment mode for customer code #' . $customer_code . ' in ' . $table_name . '.'
            );

            $this->push->send(
                ['restaurant_admin'],
                'Time To Print The Bill',
                'Admin, Order #' . $customer_code . ' at ' . $table_name . ' has been successfully completed.'
            );


            return redirect()->route('admin_order')->with('success', 'Order complete successfully!');
        } else {

            return redirect()->route('admin_order');
        }
    }

    public function updatePaymentMethod(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer',
            'payment_mode' => 'required',
        ]);

        DB::table('invoice_data')
            ->where('order_id', $request->order_id)
            ->update(['payment_mode' => $request->payment_mode]);

        return response()->json(['message' => 'Payment method updated']);
    }

    public function apply(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'total' => 'required|numeric'
        ]);

        $coupon = \App\Models\Coupon::whereRaw('BINARY `code` = ?', [$request->code])->first();

        if (!$coupon) {
            return response()->json(['error' => 'Invalid coupon code!'], 400);
        }

        if (!$coupon->isValid()) {
            return response()->json(['error' => 'Coupon has expired!'], 400);
        }

        // Check Min Order
        if (!empty($coupon->min_order) && $request->total < $coupon->min_order) {
            return response()->json(['error' => 'Minimum order required ₹' . $coupon->min_order . ''], 400);
        }

        // Check Category Specific
        if ($coupon->type == 'Category-Specific Discount') {

            $category_id = $coupon->categoryId;

            $data = json_decode(
                is_string($request->data) && str_starts_with($request->data, '"')
                    ? json_decode($request->data)
                    : $request->data
            );

            if (!$data) {
                return response()->json([
                    'error' => "Invalid cart data received!"
                ], 400);
            }

            $matchedAmount = 0;

            foreach ($data as $item) {
                $food = DB::table('items')->where('id', $item->item_id)->first();
                if ($food && $food->category_id == $category_id) {
                    $matchedAmount += ($food->price * $item->quantity);
                }
            }

            if ($matchedAmount == 0) {
                $categoryName = DB::table('categories')->where('id', $category_id)->value('category_name') ?? '';

                return response()->json([
                    'error' => "This coupon applies only to {$categoryName} items in your order!"
                ], 400);
            }

            // Now calculate discount only on matched category items
            $discount = $coupon->discount_mode === 'fixed'
                ? $coupon->value
                : ($matchedAmount * $coupon->value) / 100;
        }

        // Check Time-Based Discount
        if ($coupon->type == 'Time-Based Discount') {

            // use 24-hour format internally
            $current = now()->format('H:i:s');
            $start24 = Carbon::parse($coupon->start_time)->format('H:i:s');
            $end24   = Carbon::parse($coupon->end_time)->format('H:i:s');

            // for display only
            $start12 = Carbon::parse($coupon->start_time)->format('h:i A');
            $end12   = Carbon::parse($coupon->end_time)->format('h:i A');

            if ($current < $start24 || $current > $end24) {
                // return back()->with('error', "Coupon valid only between $start12 - $end12");

                return response()->json([
                    'error' => "Coupon valid only between $start12 - $end12"
                ], 400);
            }
        }

        // Check Days Based Discount
        if (in_array($coupon->type, ['Weekend Discount', 'Weekday Discount'])) {
            $today = strtolower(now()->format('l')); // monday, tuesday etc.
            $days = json_decode($coupon->applicable_days, true);

            if (!in_array($today, $days)) {
                return response()->json([
                    'error' => "Coupon not valid today!"
                ], 400);
            }

            $category_id = $coupon->categoryId;

            $data = json_decode(
                is_string($request->data) && str_starts_with($request->data, '"')
                    ? json_decode($request->data)
                    : $request->data
            );

            if (!$data) {
                return response()->json([
                    'error' => "Invalid cart data received!"
                ], 400);
            }
            $matchedAmount = 0;

            foreach ($data as $item) {
                $food = DB::table('items')->where('id', $item->item_id)->first();
                if ($food && $food->category_id == $category_id) {
                    $matchedAmount += ($food->price * $item->quantity);
                }
            }

            if ($matchedAmount == 0) {
                $categoryName = DB::table('categories')->where('id', $category_id)->value('category_name');
                return response()->json([
                    'error' => "This coupon applies only to $categoryName items in your order!"
                ], 400);
            }

            // Now calculate discount only on matched category items
            $discount = $coupon->discount_mode === 'fixed'
                ? $coupon->value
                : ($matchedAmount * $coupon->value) / 100;
        }

        // Check First Order
        if ($coupon->type == 'First Order Discount') {

            $tableId = session('tableId');
            $customer = DB::table('customers')
                ->join('orders', 'customers.customer_code', '=', 'orders.customer_code')
                ->where('customers.table_id', $tableId)
                ->where('orders.status', 'processed') // ✅ Only use customer with active (not completed) orders
                ->select('customers.*')
                ->first();

            $hasPhone = !empty($customer?->phone);

            if ($hasPhone == 0) {
                return response()->json([
                    'error' => "Phone number required before applying first order discount!$hasPhone"
                ], 400);
            }

            $usedCount = Customer::where('phone', $customer->phone)->count();

            if ($usedCount > 1) {
                return response()->json([
                    'error' => "This discount is only for first order!"
                ], 400);
            }
        }




        // Calculate discount
        if (!in_array($coupon->type, ['Weekday Discount', 'Category-Specific Discount'])) {
            $discount = $coupon->discount_mode === 'fixed'
                ? $coupon->value
                : ($request->total * $coupon->value) / 100;
        }

        $finalTotal = max(0, $request->total - $discount);

        $cgst = $request->cgst_rate > 0 ? ($finalTotal * $request->cgst_rate) / 100 : 0;
        $sgst = $request->sgst_rate > 0 ? ($finalTotal * $request->sgst_rate) / 100 : 0;
        $total_with_tax = $finalTotal + $cgst + $sgst;

        return response()->json([
            'success' => true,
            'discount' => number_format($discount, 2, '.', ''),
            'final_total' => number_format($finalTotal, 2, '.', ''),
            'cgst' => number_format($cgst, 2, '.', ''),
            'sgst' => number_format($sgst, 2, '.', ''),
            'total_with_tax' => number_format($total_with_tax, 2, '.', ''),
            'coupon' => $request->code
        ]);
    }

    public function suggestions(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');
        $query = $request->get('q');
        $table_id = session()->get('tableId');

        // Example: Search in `products` table by `name`
        $results = Item::where('item_name', 'LIKE', "%{$query}%")
            ->where('restaurant_id', $restaurant_id)   // ✅ fixed
            ->limit(5)
            ->get(['id', 'item_name', 'category_id']);

        return response()->json([
            'results' => $results,
            'table_id' => $table_id
        ]);
    }

    protected function sendPushToManagers($order, $extra)
    {
        $tableName = $extra['table_name'] ?? 'Unknown Table';
        $orderCode = $order->customer_code ?? 'N/A';

        $restaurant_id = session()->get('restaurant_id');

        $roles = [
            'restaurant_admin'  => "Admin, You need to set the payment mode for customer code #{$orderCode} in {$tableName}.",
        ];

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT'),
                'publicKey' => env('VAPID_PUBLIC'),
                'privateKey' => env('VAPID_PRIVATE'),
            ],
        ]);

        foreach ($roles as $role => $message) {
            $subs = PushSubscription::where('role', $role)->get();

            foreach ($subs as $sub) {
                if ($sub->restaurant_id == $restaurant_id) {
                    $data = json_decode($sub->subscription, true);

                    $webPush->sendOneNotification(
                        Subscription::create($data),
                        json_encode([
                            "title" => "New Order",
                            "body"  => $message,
                            "icon"  => "/images/order-icon.png",
                            "url"   => "/home"
                        ])
                    );
                }
            }
        }

        foreach ($webPush->flush() as $report) {
            \Log::info('Push Report', [$report->isSuccess()]);
        }
    }
}
