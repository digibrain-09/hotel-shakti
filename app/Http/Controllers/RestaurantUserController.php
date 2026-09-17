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
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

use App\Services\PushNotificationService;

require_once app_path('dompdf/autoload.inc.php');
require_once app_path('Helpers/BusinessDayHelper.php');

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class RestaurantUserController extends Controller
{
    protected PushNotificationService $push;

    public function __construct(PushNotificationService $push)
    {
        $this->push = $push;
    }
    public function index(Request $request)
    {
        $search = trim($request->search);

        $restaurants = Restaurant::all();

        $query = DB::table('restaurant_users')
            ->join('restaurants', 'restaurants.id', '=', 'restaurant_users.restaurant_id')
            ->select(
                'restaurant_users.*',
                'restaurants.restaurant_name'
            );

        // Apply search only if input exists
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('restaurant_users.name', 'LIKE', "%{$search}%")
                    ->orWhere('restaurant_users.email', 'LIKE', "%{$search}%")
                    ->orWhere('restaurant_users.role', 'LIKE', "%{$search}%")
                    ->orWhere('restaurants.restaurant_name', 'LIKE', "%{$search}%");
            });
        }

        $restaurants_users = $query->paginate(10)->withQueryString();

        return view(
            'restaurant_user.index',
            compact('restaurants', 'restaurants_users')
        );
    }

    public function checkEmail(Request $request)
    {
        $emailExists = RestaurantUser::where('email', $request->email)->exists() || User::where('email', $request->email)->exists();

        return response()->json([
            'exists' => $emailExists
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required',
        ]);

        $data = new RestaurantUser();
        $data2 = new User();

        $data->name = $request->name;
        $data->email = $request->email;
        $data->password = Hash::make($request->password);
        $data->restaurant_id = $request->restaurantId;
        $data->role = $request->role;
        $data->save();

        $data2->name = $request->name;
        $data2->email = $request->email;
        $data2->password = Hash::make($request->password);
        $data2->role = $request->role;
        $data2->save();


        // return redirect()->back()->with('success', 'User added successfully!');


        return response()->json([
            'success' => true,
            'message' => 'User added successfully!'
        ]);
    }

    public function update(Request $request, $id)
    {
        $data = RestaurantUser::find($id);

        $users = User::where('email', $data->email)->get();

        foreach ($users as $user) {
            $user->name = $request->input('name');
            $user->email = $request->input('email');
            if ($request->input('password')) {
                $user->password = Hash::make($request->input('password'));
            }
            $user->role = $request->input('role');
            $user->save(); // Use save() instead of update()
        }

        $data->name = $request->input('name');
        $data->email = $request->input('email');
        if ($request->input('password')) {
            $data->password = Hash::make($request->input('password'));
        }
        $data->restaurant_id = $request->input('restaurantId');
        $data->role = $request->input('role');
        $data->save();


        // return redirect()->route('restaurant_user.index');
        // return redirect()->back()->with('success', 'User updated successfully!');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!'
        ]);
    }

    public function destroy(RestaurantUser $restaurantUser)
    {
        User::where('email', $restaurantUser->email)->delete();

        $restaurantUser->delete();
        // return redirect()->route('restaurant_user.index');
        return redirect()->back()->with('success', 'User deleted successfully!');
    }

    public function manager_order(Request $request)
    {

        $show_session = session()->get('session_code');
        $restaurant_id = session()->get('restaurant_id');

        $restaurant = Restaurant::where('id', $restaurant_id)->get();

        $tableCount = Table::where('restaurant_id', $restaurant_id)->count();

        $tableAirCount = Table::where('restaurant_id', $restaurant_id)->where('seating_type', true)->count();

        $tableNonAirCount = Table::where('restaurant_id', $restaurant_id)->where('seating_type', false)->count();


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





        return view('manager_order.index', compact('tables', 'customer', 'restaurant', 'tableCount', 'availableTableCount', 'tableAirCount', 'tableNonAirCount'));
    }

    public function getManagerOrders(Request $request)
    {
        [$start, $end] = getBusinessTimeRange();

        $newOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('orders.is_notified', 'false')
            ->select('orders.customer_code', 'restaurant_tables.table_name')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        $restaurant_id = session()->get('restaurant_id');

        $orderdata = DB::table('orders')
            ->selectRaw('orders.*')
            ->get();

        // Get the latest orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id')
            ->groupBy('customer_code');


        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('order_print', 'order_print.customer_code', '=', 'order_counts.customer_code')
            ->where('customers.table_id', $request->table_id)
            // ->whereDate('order_counts.latest_created_at', '=', today())
            ->whereDate('order_counts.latest_created_at', '=', [$start, $end])
            ->select('customers.customer_code', 'order_counts.count', 'order_counts.status', 'order_counts.latest_created_at', 'order_counts.latest_updated_at', 'order_counts.id', 'order_print.status as order_print_status')
            ->orderBy('order_counts.latest_created_at', 'DESC')
            ->orderBy('order_counts.status', 'DESC')
            ->paginate(10)
            ->appends(request()->query()); // Add this line to maintain query parameters
        // return $orders;


        // $last = DB::table('orders')->latest()->select('orders.customer_code')->first();

        $last = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('orders.customer_code')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->first();


        $data = DB::table('orders')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->selectRaw('orders.*,order_print.item_data')
            ->get();

        $order_print = DB::table('order_print')
            ->selectRaw('order_print.*')
            ->get();

        return view('manager_order.order', compact('orders', 'data', 'last', 'order_print'));
    }

    function getLatestManagerOrders(Request $request)
    {
        [$start, $end] = getBusinessTimeRange();
        
        $restaurant_id = session()->get('restaurant_id');

        // Get the latest orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(order_confirm_by) as order_confirm_by')
            ->groupBy('customer_code');


        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('order_print', 'order_print.customer_code', '=', 'order_counts.customer_code')
            ->where('customers.table_id', $request->table_id)
            // ->whereDate('order_counts.latest_created_at', '=', today())
            ->whereDate('order_counts.latest_created_at', '=', [$start, $end])
            ->select('customers.customer_code', 'order_counts.count', 'order_counts.status', 'order_counts.latest_created_at', 'order_counts.latest_updated_at', 'order_counts.id', 'order_print.status as order_print_status', 'order_counts.order_confirm_by')
            ->orderBy('order_counts.latest_created_at', 'DESC')
            ->orderBy('order_counts.status', 'DESC')
            ->paginate(10)
            ->appends(request()->query()); // Add this line to maintain query parameters
        // return $orders;

        $data = DB::table('orders')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->selectRaw('orders.*,order_print.item_data')
            ->get();

        $order_print = DB::table('order_print')
            ->selectRaw('order_print.*')
            ->get();

        $last = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('orders.customer_code')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->first();

        $newOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('orders.is_notified', 'false')
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();


        return response()->json(['orders' => $orders, 'data' => $data, 'order_print' => $order_print, 'last' => $last, 'newOrders' => $newOrders]);
    }



    public function RemoveOrderItem($orderId, $itemId)
    {
        // 1. Find the order by ID
        $order = DB::table('orders')->where('id', $orderId)->first();

        if (!$order) {
            return redirect()->back()->with('error', 'Order not found');
        }

        // 2. Decode the JSON data
        $jsonData = json_decode($order->data, true);

        // 3. Ensure $jsonData is an array before filtering
        if (!is_array($jsonData)) {
            return redirect()->back()->with('error', 'Invalid data format');
        }

        // 4. Remove the specific item from JSON
        $filteredData = array_filter($jsonData, function ($item) use ($itemId) {
            return $item['item_id'] != $itemId;  // Keep all items except the one to delete
        });

        // 5. Recalculate the total
        $total = array_reduce($filteredData, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);





        // 1. Find the order by ID
        $order_print = DB::table('order_print')->where('order_id', $orderId)->first();

        if (!$order_print) {
            return redirect()->back()->with('error', 'Order not found');
        }

        // 2. Decode the JSON data
        $jsonData2 = json_decode($order_print->item_data, true);

        // 3. Ensure $jsonData is an array before filtering
        if (!is_array($jsonData2)) {
            return redirect()->back()->with('error', 'Invalid data format');
        }

        // 4. Find the price of the item being removed
        $removedItem = array_filter($jsonData2, function ($item) use ($itemId) {
            return $item['item_id'] == $itemId;  // Find the item to be removed
        });

        // Calculate the price to subtract
        $priceToSubtract = 0;
        if (!empty($removedItem)) {
            foreach ($removedItem as $item) {
                $priceToSubtract += $item['price'] * $item['quantity'];

                if (!empty($item['addons']) && $item['addons'] !== "null") {
                    $addons = json_decode($item['addons'], true);
                    if (is_array($addons)) {
                        foreach ($addons as $addon) {
                            $priceToSubtract += (float) $addon['price'] * (int) $item['quantity'];
                        }
                    }
                }
            }
        }

        // 5. Remove the specific item from JSON
        $filteredData2 = array_filter($jsonData2, function ($item) use ($itemId) {
            return $item['item_id'] != $itemId;  // Keep all items except the one to delete
        });

        // 6. Recalculate the total amount from remaining items
        $newTotal = array_reduce($filteredData2, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        // 7. Check if any remaining item has "print" = "Yes"
        $hasPrintYes = array_reduce($filteredData2, function ($carry, $item) {
            return $carry || ($item['print'] === 'No');
        }, false);

        // return $priceToSubtract;


        // 8. Update the database with new JSON data
        DB::table('order_print')->where('id', $order_print->id)->update([
            'item_data' => json_encode(array_values($filteredData2)), // Re-index array
            'status' => $hasPrintYes ? 0 : 1 // Update status based on remaining items
        ]);

        // 9. Update the orders table: subtract the removed item's price from total_amount
        DB::table('orders')->where('id', $order_print->order_id)->update([
            'data' => json_encode(array_values($filteredData)),
            'total_amount' => DB::raw("total_amount - $priceToSubtract"),
            'order_confirm' => $hasPrintYes ? 0 : 1 // Update status based on remaining items
        ]);


        // 7. Redirect with success message
        return redirect()->back()->with('success', 'Item removed successfully!')->with('total', $total);
    }

    public function PrintOrder($id)
    {
        $restaurant_id = session()->get('restaurant_id');
        $kitchen_order_status = 'sent_to_kitchen';

        $orders = DB::table('order_print')
            ->where('order_id', $id)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found'], 404);
        }

        // --- below code runs ONLY when window.open loads page ---

        foreach ($orders as $order) {
            $tableId = $order->table_id;
            $item_data = json_decode($order->item_data, true);
            $item_data_for_print = $item_data;
            $date = $order->updated_at;
            $customer_code = $order->customer_code;

            $tables = DB::table('restaurant_tables')
                ->where('id', $order->table_id)
                ->first();

            foreach ($item_data as &$item) {
                $item['print'] = 'Yes';
            }

            DB::table('order_print')
                ->where('order_id', $id)
                ->update([
                    'item_data' => json_encode($item_data),
                    'status' => 1
                ]);

            $this->push->send(
                ['kitchen_owner'],
                'New Order',
                'Kitchen, New order ' . $customer_code . ' at ' . $tables->table_name . '.'
            );

            Order::where('id', $id)->update(['order_confirm' => true, 'kitchen_order_status' => $kitchen_order_status, 'kitchen_order_notified' => false, 'manager_kitchen_order_notified' => false, 'waiter_kitchen_order_notified' => true]);
        }

        return response()->json([
            'success' => true,
            'message' => 'You can print now'
        ]);
    }


    public function UpdateOrderQuantity(Request $request)
    {
        try {
            // Find the order records
            $order_print = DB::table('order_print')->where('order_id', $request->orderId)->first();
            $order = DB::table('orders')->where('id', $request->orderId)->first();

            if (!$order_print || !$order) {
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Decode JSON
            $items = json_decode($order_print->item_data, true);
            $orderitems = json_decode($order->data, true);

            if (!is_array($items) || !is_array($orderitems)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid item data'], 400);
            }

            // Update order_print
            foreach ($items as &$item) {
                if ($item['item_id'] == $request->itemId) {
                    $item['quantity'] = (string) $request->quantity;

                    // handle addons if present
                    if (!empty($item['addons']) && $item['addons'] !== "null") {
                        $addons = json_decode($item['addons'], true);
                        if (is_array($addons)) {
                            foreach ($addons as &$addon) {
                                // multiply addon price by quantity
                                $addon['total_price'] = (float) $addon['price'] * (int) $request->quantity;
                            }
                            $item['addons'] = json_encode($addons); // save back as string
                        }
                    }
                }
            }

            // Update orders
            foreach ($orderitems as &$item) {
                if ($item['item_id'] == $request->itemId) {
                    $item['quantity'] = (string) $request->quantity;

                    if (!empty($item['addons']) && $item['addons'] !== "null") {
                        $addons = json_decode($item['addons'], true);
                        if (is_array($addons)) {
                            foreach ($addons as &$addon) {
                                $addon['total_price'] = (float) $addon['price'] * (int) $request->quantity;
                            }
                            $item['addons'] = json_encode($addons);
                        }
                    }
                }
            }

            /**
             * 🔹 Recalculate Total Amount
             */
            $totalAmount = 0;

            foreach ($orderitems as $item) {
                $itemTotal = (float) $item['price'] * (int) $item['quantity'];

                $addonTotal = 0;
                if (!empty($item['addons']) && $item['addons'] !== "null") {
                    $addons = json_decode($item['addons'], true);
                    if (is_array($addons)) {
                        foreach ($addons as $addon) {
                            $addonTotal += (float) $addon['price'] * (int) $item['quantity'];
                        }
                    }
                }

                $totalAmount += $itemTotal + $addonTotal;
            }


            // Save back
            DB::table('order_print')
                ->where('order_id', $request->orderId)
                ->update([
                    'item_data' => json_encode($items),
                    'updated_at' => now(),
                ]);

            DB::table('orders')
                ->where('id', $request->orderId)
                ->update([
                    'data' => json_encode($orderitems),
                    'total_amount' => $totalAmount,
                    'updated_at' => now(),
                ]);

            return response()->json(['status' => 'success', 'message' => 'Quantity & addons updated']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function view_item(Request $request)
    {
        // $table_id = session()->get('tableId');
        $seating_type = session()->get('seating_type');

        // Get cart data for this item and table
        $order_print = DB::table('order_print')->where('order_id', $request->orderId)->first();

        if (!$order_print) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        $orderitems = json_decode($order_print->item_data, true);
        if (!is_array($orderitems)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid item data'], 400);
        }

        // Find the matching item
        $selectedItem = null;
        foreach ($orderitems as $itm) {
            if ($itm['item_id'] == $request->itemId) {
                $selectedItem = $itm;
                break;
            }
        }

        if (!$selectedItem) {
            return response()->json(['status' => 'error', 'message' => 'Item not found in order'], 404);
        }

        // Decode addons stored in order_print
        $selectedAddons = [];
        if (!empty($selectedItem['addons']) && $selectedItem['addons'] !== "null") {
            $selectedAddons = json_decode($selectedItem['addons'], true) ?? [];
        }

        // Get master item + all possible addons
        $items = DB::table('items')
            ->leftJoin('item_addons', 'items.id', '=', 'item_addons.item_id')
            ->leftJoin('add_ons', 'add_ons.id', '=', 'item_addons.addon_id')
            ->where('items.id', $request->itemId)
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


        // Build addons response (mark selected ones)
        $addons = $items->filter(fn($row) => $row->addon_id !== null)
            ->map(function ($row) use ($selectedAddons, $selectedItem) {
                $isSelected = collect($selectedAddons)->contains(function ($sel) use ($row) {
                    return is_array($sel) ? $sel['id'] == $row->addon_id : $sel == $row->addon_id;
                });

                return [
                    'id' => $row->addon_id,
                    'name' => $row->addon_name,
                    'price' => $row->addon_price,
                    'type' => $row->addon_type,
                    'selected' => $isSelected,
                    'total_price' => $isSelected ? (float) $row->addon_price * (int) $selectedItem['quantity'] : 0
                ];
            })
            ->values();

        // Return response
        return response()->json([
            'item_id' => $first->item_id,
            'name' => $first->item_name,
            'picture' => $first->picture,
            'price' => $seating_type == 1 ? $first->item_ac_price : $first->item_price,
            'description' => $first->description,
            'food_type' => $first->food_type,
            'add_ons' => $addons,
            'note' => $selectedItem['note'] ?? null,
            'quantity' => $selectedItem['quantity'],
            'order_id' => $request->orderId,
        ]);
    }

    public function UpdateManagerOrder(Request $request)
    {
        try {
            // Find the order records
            $order_print = DB::table('order_print')->where('order_id', $request->orderId)->first();
            $order = DB::table('orders')->where('id', $request->orderId)->first();

            if (!$order_print || !$order) {
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Decode JSON
            $items = json_decode($order_print->item_data, true);
            $orderitems = json_decode($order->data, true);

            if (!is_array($items) || !is_array($orderitems)) {
                return response()->json(['status' => 'error', 'message' => 'Invalid item data'], 400);
            }

            // Format addons from request
            $newAddons = [];
            if (!empty($request->addons)) {
                foreach ($request->addons as $addon) {
                    $newAddons[] = [
                        'id'          => $addon['id'],
                        'name'        => $addon['name'],
                        'price'       => (float) $addon['price'],
                        'total_price' => (float) $addon['price'] * (int) $request->quantity,
                    ];
                }
            }

            // 🔹 Update order_print
            foreach ($items as &$item) {
                if ($item['item_id'] == $request->itemId) {
                    $item['quantity'] = (string) $request->quantity;
                    $item['note']     = $request->note;
                    $item['addons']   = json_encode($newAddons); // Replace addons
                }
            }

            // 🔹 Update orders
            foreach ($orderitems as &$item) {
                if ($item['item_id'] == $request->itemId) {
                    $item['quantity'] = (string) $request->quantity;
                    $item['note']     = $request->note;
                    $item['addons']   = json_encode($newAddons);
                }
            }

            // 🔹 Recalculate total
            $totalAmount = 0;
            foreach ($orderitems as &$item) {
                $itemTotal = (float) $item['price'] * (int) $item['quantity'];

                $addonTotal = 0;
                if (!empty($item['addons']) && $item['addons'] !== "null") {
                    $addons = json_decode($item['addons'], true);
                    if (is_array($addons)) {
                        foreach ($addons as $addon) {
                            $addonTotal += (float) $addon['price'] * (int) $item['quantity'];
                        }
                    }
                }

                $totalAmount += $itemTotal + $addonTotal;
            }

            // 🔹 Save back
            DB::table('order_print')
                ->where('order_id', $request->orderId)
                ->update([
                    'item_data'  => json_encode($items),
                    'updated_at' => now(),
                ]);

            DB::table('orders')
                ->where('id', $request->orderId)
                ->update([
                    'data'         => json_encode($orderitems),
                    'total_amount' => $totalAmount,
                    'updated_at'   => now(),
                ]);

            return response()->json([
                'status'       => 'success',
                'message'      => 'Quantity, addons & total updated',
                'total_amount' => $totalAmount
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
