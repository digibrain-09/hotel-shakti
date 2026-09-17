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
use App\Models\KitchenOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Services\PushNotificationService;

require_once app_path('dompdf/autoload.inc.php');
require_once app_path('Helpers/BusinessDayHelper.php');

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class KitchenController extends Controller
{
    protected PushNotificationService $push;

    public function __construct(PushNotificationService $push)
    {
        $this->push = $push;
    }
    public function index()
    {
        return view('kitchen_order.new-order');
    }

    public function getKitchenOrders()
    {
        $restaurant_id = session()->get('restaurant_id');
        [$start, $end] = getBusinessTimeRange();

        // ✅ 1. Get Orders (without table join)
        // $orders = Order::whereDate('orders.created_at', today())
        //     ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
        //     ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
        //     ->where('orders.dispatch', false)
        //     ->where('customers.restaurant_id', $restaurant_id)
        //     ->select(
        //         'orders.*',
        //         'customers.table_id',
        //         'order_print.item_data'
        //     )
        //     ->latest()
        //     ->get();

        $orders = Order::whereDate('orders.created_at', '=', [$start, $end])
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('orders.dispatch', false)
            ->where('customers.restaurant_id', $restaurant_id)
            ->select(
                'orders.*',
                'customers.table_id',
                'order_print.item_data'
            )
            ->latest()
            ->get();

        // ✅ 2. Get Tables separately
        $tables = Table::where('restaurant_id', $restaurant_id)
            ->pluck('table_name', 'id');
        // returns: [ id => table_name ]

        return response()->json([
            'orders' => $orders,
            'tables' => $tables
        ]);
    }

    function getLatestKitchenOrders(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');
        [$start, $end] = getBusinessTimeRange();

        // Get the latest orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(order_confirm) as order_confirm,MAX(order_confirm_by) as order_confirm_by')
            ->groupBy('customer_code');


        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('order_print', 'order_print.customer_code', '=', 'order_counts.customer_code')
            ->where('customers.table_id', $request->table_id)
            // ->where('order_counts.order_confirm', true)
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
            ->where('orders.kitchen_order_notified', false)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();


        return response()->json(['orders' => $orders, 'data' => $data, 'order_print' => $order_print, 'last' => $last, 'newOrders' => $newOrders]);
    }

    function checkNewKitchenOrders()
    {
        $adminEmail = auth()->user()->email;

        $newOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('restaurant_users', 'restaurant_users.restaurant_id', '=', 'customers.restaurant_id')
            ->where('orders.kitchen_order_status', 'sent_to_kitchen')
            ->where('orders.kitchen_order_notified', false)
            ->where('restaurant_users.email', $adminEmail)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        $newTakeAwayOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_users', 'restaurant_users.restaurant_id', '=', 'customers.restaurant_id')
            ->where('orders.kitchen_order_status', 'sent_to_kitchen')
            ->where('orders.order_type', 'Take Away')
            ->where('orders.kitchen_order_notified', false)
            ->where('restaurant_users.email', $adminEmail)
            ->select('orders.customer_code', 'orders.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        return response()->json(['newOrders' => $newOrders, 'newTakeAwayOrders' => $newTakeAwayOrders]);
    }

    public function updateKitchenOrderNotified($orderId)
    {
        // Find the order by ID and update the 'is_notified' flag to true
        Order::where('id', $orderId)
            ->update(['kitchen_order_notified' => true]);

        // Return a JSON response to indicate success
        return response()->json(['success' => true]);
    }

    public function ItemReadyToServed(Request $request, $orderId, $itemId)
    {
        $restaurant_id = session()->get('restaurant_id');
        $status = $request->status; // Yes / No
        $kitchen_order_status = 'ready';

        $orders = DB::table('order_print')
            ->join('orders', 'orders.customer_code', '=', 'order_print.customer_code')
            ->where('order_id', $orderId)
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found'], 404);
        }

        foreach ($orders as $order) {
            $item_data = json_decode($order->item_data, true);
            $orderCode = $order->customer_code;
            $orderConfirmby = $order->order_confirm_by;

            $tables = DB::table('restaurant_tables')
                ->where('id', $order->table_id)
                ->first();

            foreach ($item_data as &$item) {
                if ($item['item_id'] == $itemId) {
                    $item['ReadytoServed'] = $status;
                    $item['print'] = 'Yes';
                    $item['alerted'] = 'No';
                }
            }

            $pendingItems = collect($item_data)->where('ReadytoServed', 'No')->count();

            DB::table('order_print')
                ->where('order_id', $orderId)
                ->update([
                    'item_data' => json_encode($item_data),
                    'status' => 1
                ]);

            // 🔔 Send push ONLY when marked Yes
            if ($status === 'Yes') {

                if ($orderConfirmby === 'manager') {
                    if ($order->order_type == 'Dine In') {
                        $this->push->send(
                            ['restaurant_admin'],
                            'Order Ready To Serve',
                            'Admin, Order #' . $orderCode . ' is ready at ' . $tables->table_name . '.'
                        );

                        Order::where('id', $orderId)->update([
                            'manager_kitchen_order_notified' => false,
                            'waiter_kitchen_order_notified' => true,
                            'kitchen_order_status' => $pendingItems === 0 ? $kitchen_order_status : 'sent_to_kitchen'
                        ]);
                    } else if ($order->order_type == 'Take Away') {
                        $this->push->send(
                            ['restaurant_admin'],
                            'Order Is Ready',
                            'Admin, Order #' . $orderCode . ' is ready for take away.'
                        );

                        Order::where('id', $orderId)->update([
                            'order_notified' => false,
                            'manager_kitchen_order_notified' => true,
                            'waiter_kitchen_order_notified' => true,
                            'kitchen_order_status' => $pendingItems === 0 ? $kitchen_order_status : 'sent_to_kitchen'
                        ]);
                    }
                } elseif ($orderConfirmby === 'waiter') {
                    $this->push->send(
                        ['restaurant_admin'],
                        'Order Ready To Serve',
                        'Admin, Order #' . $orderCode . ' is ready at ' . $tables->table_name . '.'
                    );

                    Order::where('id', $orderId)->update([
                        'waiter_kitchen_order_notified' => false,
                        'manager_kitchen_order_notified' => true,
                        'kitchen_order_status' => $pendingItems === 0 ? $kitchen_order_status : 'sent_to_kitchen'
                    ]);
                }
            } else if ($status === 'No') {
                if ($orderConfirmby === 'manager') {

                    Order::where('id', $orderId)->update([
                        'manager_kitchen_order_notified' => false,
                        'waiter_kitchen_order_notified' => true,
                        'kitchen_order_status' => $pendingItems === 0 ? $kitchen_order_status : 'sent_to_kitchen'
                    ]);
                } elseif ($orderConfirmby === 'waiter') {

                    Order::where('id', $orderId)->update([
                        'waiter_kitchen_order_notified' => false,
                        'manager_kitchen_order_notified' => true,
                        'kitchen_order_status' => $pendingItems === 0 ? $kitchen_order_status : 'sent_to_kitchen'
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => $status === 'Yes'
                ? 'Item marked as Ready to Serve.'
                : 'Item status reverted successfully.'
        ]);
    }


    public function updateManagerKitchenOrderNotified($orderId)
    {
        // Find the order by ID and update the 'is_notified' flag to true
        Order::where('id', $orderId)
            ->update(['manager_kitchen_order_notified' => true]);

        $order = DB::table('order_print')
            ->where('order_id', $orderId)
            ->first();

        $items = json_decode($order->item_data, true);

        foreach ($items as &$item) {
            if ($item['ReadytoServed'] === 'Yes') {
                $item['alerted'] = 'Yes';
            }
        }

        DB::table('order_print')
            ->where('order_id', $orderId)
            ->update([
                'item_data' => json_encode($items)
            ]);


        // Return a JSON response to indicate success
        return response()->json(['success' => true]);
    }

    public function updateWaiterKitchenOrderNotified($orderId)
    {
        // Find the order by ID and update the 'is_notified' flag to true
        Order::where('id', $orderId)
            ->update(['waiter_kitchen_order_notified' => true]);

        $order = DB::table('order_print')
            ->where('order_id', $orderId)
            ->first();

        $items = json_decode($order->item_data, true);

        foreach ($items as &$item) {
            if ($item['ReadytoServed'] === 'Yes') {
                $item['alerted'] = 'Yes';
            }
        }

        DB::table('order_print')
            ->where('order_id', $orderId)
            ->update([
                'item_data' => json_encode($items)
            ]);


        // Return a JSON response to indicate success
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request)
    {

        $order = Order::find($request->order_id);
        $order->dispatch = true;
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Status updated'
        ]);
    }
}
