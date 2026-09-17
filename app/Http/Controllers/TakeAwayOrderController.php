<?php

namespace App\Http\Controllers;

use App\Models\CartTakeAway;
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
use App\Models\Cart;
use App\Models\KitchenOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Item;
use App\Models\Invoice;

use Illuminate\Support\Facades\Cookie;
use App\Jobs\Jobname–queued;
use Carbon\Carbon;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;

use App\Services\PushNotificationService;
use Illuminate\Support\Str;


require_once app_path('dompdf/autoload.inc.php');
require_once app_path('Helpers/BusinessDayHelper.php');

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class TakeAwayOrderController extends Controller
{
    protected PushNotificationService $push;

    public function __construct(PushNotificationService $push)
    {
        $this->push = $push;
    }
    public function index()
    {
        $restaurant_id = session()->get('restaurant_id');

        if (!$restaurant_id) {
            return redirect()->route('home')->with('msg', 'Invalid session data.');
        }

        $cartToken = session()->get('takeaway_cart_token');

        if (!$cartToken) {
            $cartToken = (string) Str::uuid();

            session()->put('takeaway_cart_token', $cartToken);
        }

        $categories = Category::where('restaurant_id', $restaurant_id)->get();
        $customers = DB::table('orders')->latest('created_at')->first();
        $cart = CartTakeAway::where('cart_token', $cartToken)
        ->sum('quantity');
        $catTab = $categories->first()->id ?? null;

        $cart_items = CartTakeAway::where('cart_token', $cartToken)->get();


        return view('order.take-away-category', compact('categories', 'catTab', 'cart', 'customers', 'cart_items'));
    }

    public function getOrderItems($category_id)
    {
        $cartToken = session()->get('takeaway_cart_token');
        $items = Item::where('category_id', $category_id)->get();
        $cart = count((is_countable(session()->get('cart')) ? session()->get('cart') : []));
        $cart = CartTakeAway::where('cart_token', $cartToken)->count();


        $cart_items = CartTakeAway::where('cart_token', $cartToken)->get();


        $cartItems = CartTakeAway::where('cart_token', $cartToken)->pluck('quantity', 'item_id')->toArray();

        $add_ons = DB::table('item_addons')
            ->get(['item_addons.*']);


        return view('order.take-away-items', compact('items', 'cart', 'cart_items', 'cartItems', 'add_ons'));
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

        $cartToken = session()->get('takeaway_cart_token');
        // Get cart data for this item and table
        $cart = DB::table('carts_takeaway')
        ->where('cart_token', $cartToken)
            ->where('item_id', $id)
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

    public function AddtoCart(Request $request)
    {

        $cartToken = session()->get('takeaway_cart_token');
        if (!$cartToken) {
            $cartToken = (string) Str::uuid();

            session()->put('takeaway_cart_token', $cartToken);
        }
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

        $cart = new CartTakeAway();
        $cart->cart_token = $cartToken;
        $cart->item_id = $itemId;
        $cart->name = $item_name;
        $cart->price = $price;
        $cart->quantity = $quantity;
        $cart->addons = json_encode($addons);;
        $cart->note = $note;
        $cart->image = $item_image;
        $cart->save();

        return response()->json(['success' => true]);
    }

    public function RemoveOrderfromCart($id)
    {
        $cartToken = session()->get('takeaway_cart_token');
        $cart = CartTakeAway::where('cart_token', $cartToken)->find($id);
        $cart->delete();
        return redirect()->back();
    }

    public function UpdateTakeAwayQuantity(Request $request)
    {
        $cartToken = session()->get('takeaway_cart_token');
        if ($request->id) {
            $cart = CartTakeAway::where('cart_token', $cartToken)->find($request->id);
            $cart->quantity = $request->quantity;
            $cart->update();
        }
    }

    public function update(Request $request)
    {

        $item_id = $request->input('item_id');
        $action = $request->input('action');
        $cartToken = session()->get('takeaway_cart_token');

        $cartItem = CartTakeAway::where('cart_token', $cartToken)->where('item_id', $item_id)->first();

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

    public function TakeAwayOrder(Request $request)
    {

        $session_code = Cookie::get('CustomerOrderId');

        $restaurant_id = session()->get('restaurant_id');
        $cartToken = session()->get('takeaway_cart_token');

        // If no customer found, create a new customer and assign customer_code
        $code = "CUST" . rand(10000, 99999999);
        $minutes = 5 * 60;
        Cookie::queue('CustomerOrderId', $code, $minutes);

        // Create a new customer
        $customer = new Customer();
        $customer->customer_code = $code;
        $customer->table_id = 0;
        $customer->restaurant_id = $restaurant_id;
        $customer->phone = $request->phone;
        $customer->save();

        $customer_code = $code;  // Set customer_code for new customer

        session()->put('customer_code', $customer_code);

        // Fetch the cart items
        $cart = CartTakeAway::where('cart_token', $cartToken)->get();
        $status = 'processed';
        $kitchen_order_status = 'sent_to_kitchen';
        $is_notified = 'true';
        $order_confirm_by = "manager";
        $order_type = 'Take Away';
        $total_amount = $request->total_amount;


        // If no existing orders, create a new order
        $json_data = [];
        $order = new Order();
        $order->customer_code = $customer_code; // Use the customer_code
        $order->order_type = $order_type;

        $items = DB::table('items')
        ->where('restaurant_id', $restaurant_id)
        ->get()
        ->keyBy('id');

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
        $orderprint->table_id = 0;
        $orderprint->save();

        $this->push->send(
            ['kitchen_owner'],
            'New Order',
            'Kitchen, New Take Away Order from ' . $customer_code . ' Please check.'
        );

        // $this->push->send(
        //     ['restaurant_admin'],
        //     'New Order',
        //     'Admin, New Take Away Order from ' . $customer_code . ' Please check.'
        // );

        // Dispatch jobs for each cart item
        foreach ($cart as $number => $val) {
            $details['customer_code'] = $customer_code;  // Ensure the correct customer code is used
            $details['item_id'] = $val['item_id'];
            $details['quantity'] = $val['quantity'];

            Jobname–queued::dispatch($details)->delay(now()->addYear());
        }

        // Delete cart items after order is processed
        DB::table('carts_takeaway')->where('cart_token', $cartToken)->delete();

        // return redirect()->route('home')->with('success', 'Order placed successfully!');
        return response()->json([
            'success' => true,
            'order_id' => $order_id
        ]);
    }

    public function PrintOrder($id)
    {
        $restaurant_id = session()->get('restaurant_id');
        $line_items = [];
        $payment_method = 'TakeAway_' . uniqid();

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

            Order::where('id', $id)->update(['order_confirm' => true, 'order_notified' => false, 'kitchen_order_notified' => false, 'manager_kitchen_order_notified' => true, 'waiter_kitchen_order_notified' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'You can print now'
        ]);
    }
    public function ViewOrder()
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

        [$start, $end] = getBusinessTimeRange();


        $restaurant_id = session()->get('restaurant_id');

        $orderdata = DB::table('orders')
            ->selectRaw('orders.*')
            ->get();

        // Get the latest orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(order_confirm_by) as order_confirm_by,MAX(order_type) as order_type')
            ->groupBy('customer_code');


        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('order_print', 'order_print.customer_code', '=', 'order_counts.customer_code')
            // ->join('invoice_data', 'invoice_data.order_id', '=', 'order_counts.id')
            ->whereDate('order_counts.latest_created_at', '=', [$start, $end])
            ->where('order_counts.order_type', '=', 'Take Away')
            ->where('customers.restaurant_id', '=', $restaurant_id)
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
            ->where('orders.status', 'processed')
            ->get(['orders.*', 'customers.restaurant_id']);


        $restaurant = DB::table('restaurants')->where('id', $restaurant_id)->first();





        // return $orders2;

        if (isset($order_id)) {
            $invoice_data = DB::table('invoice_data')
                ->get();
        }

        $last = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('orders.customer_code')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->first();

        return view('order.view-take-away-order', compact(
            'orders',
            'data',
            'orders2',
            'order_print',
            'invoice_data',
            'last',
            'restaurant',
            'checkout_with_tax',
            'cgst_rate',
            'sgst_rate',
            'cgst',
            'sgst',
            'total_with_tax',
            'GST_status',
            'total' ,
        ));
    }

    public function ViewLatestOrder()
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

        [$start, $end] = getBusinessTimeRange();



        $restaurant_id = session()->get('restaurant_id');

        $orderdata = DB::table('orders')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->selectRaw('orders.*,order_print.item_data')
            ->get();

        // Get the latest orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(order_confirm_by) as order_confirm_by,MAX(order_type) as order_type')
            ->groupBy('customer_code');


        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('order_print', 'order_print.customer_code', '=', 'order_counts.customer_code')
            // ->join('invoice_data', 'invoice_data.order_id', '=', 'order_counts.id')
            ->whereDate('order_counts.latest_created_at', '=', [$start, $end])
            ->where('order_counts.order_type', '=', 'Take Away')
            ->where('customers.restaurant_id', '=', $restaurant_id)
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
                ->get();
        }

        $last = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('orders.customer_code')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->first();

        return response()->json([
            'orders' => $orders,
            'orderdata' => $orderdata,
            'data' => $data,
            'orders2' => $orders2,
            'order_print' => $order_print,
            'checkout_with_tax' => $checkout_with_tax,
            'cgst_rate' => $cgst_rate,
            'sgst_rate' => $sgst_rate,
            'cgst'           => $cgst,
            'sgst'           => $sgst,
            'total_with_tax' => $total_with_tax,
            'invoice_data'         => $invoice_data,
            'GST_status' => $GST_status,
            'total'       => $total,
            'last'    => $last,

        ]);
        // return view('order.view-take-away-order', compact('orders', 'data', 'orders2', 'order_print', 'checkout_with_tax', 'cgst_rate', 'sgst_rate', 'cgst', 'sgst', 'total_with_tax', 'invoice_data', 'GST_status', 'total', 'last'));
    }
    public function CompleteTakeAwayOrder(Request $request)
    {

        $line_items = [];
        $payment_method = 'TakeAway_' . uniqid();
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

        $table_name = '';

        $order = Order::findOrFail($request->order_id);
        
        if (!empty($line_items)) {

            // Generate PDF invoice
            $invoiceData = [
                'table' => '',
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

            $restaurant = DB::table('restaurants')->where('id', session('restaurant_id'))->first();

            DB::table('orders')->where('id', $orderId)->update([
                'cgst' => $request->cgst,
                'sgst' => $request->sgst,
                'taxable_amount' => $request->total_with_tax,
                'coupon_code' => $request->coupon,
                'discount' => $request->discount,
            ]);

            $html = View::make('client.invoice',  ['order' => $order,'restaurant' => $restaurant, 'invoiceData' => $invoiceData])->render();

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

            session()->forget(['cgst', 'sgst', 'total_with_tax', 'coupon', 'discount','takeaway_cart_token']);

            $currentOrder = $existingCustomer; // your saved order

            // $this->push->send(
            //     ['restaurant_waiter'],
            //     'Payment Mode',
            //     'Waiter, You need to set the payment mode for customer code #' . $customer_code . ' in ' . $table_name . '.'
            // );

            // $this->push->send(
            //     ['restaurant_admin'],
            //     'Time To Print The Bill',
            //     'Admin, Order #' . $customer_code . ' at ' . $table_name . ' has been successfully completed by the waiter.'
            // );

            return redirect()->back()->with('success', 'Order complete successfully!');
        } else {

            return redirect()->route('ViewOrder');
        }
    }

    public function SearchSuggestions(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');
        $query = $request->get('q');

        // Example: Search in `products` table by `name`
        $results = Item::where('item_name', 'LIKE', "%{$query}%")
            ->where('restaurant_id', $restaurant_id)   // ✅ fixed
            ->limit(5)
            ->get(['id', 'item_name', 'category_id']);

        return response()->json([
            'results' => $results,
        ]);
    }

    public function ViewTakeAwayInvoice(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $restaurant_id = session()->get('restaurant_id');
        $invoiceData = DB::table('invoice_data')
            ->join('orders', 'orders.customer_code', '=', 'invoice_data.customer_code')
            ->where('invoice_data.restaurant_id', $restaurant_id)
            ->where('orders.order_type', 'Take Away')
            ->whereBetween('invoice_data.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('invoice_data.*, orders.order_confirm_by')
            ->orderBy('invoice_data.created_at', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        return view('invoice.take-away-invoice', compact('invoiceData', 'startDate', 'endDate'));
    }
}
