<?php

namespace App\Http\Controllers;

use App\Jobs\Jobname–queued;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Queue;
use App\Models\Restaurant;
use App\Models\Invoice;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;



require_once app_path('dompdf/autoload.inc.php');
require_once app_path('Helpers/BusinessDayHelper.php');

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $restaurant_id = session('restaurant_id');
        $search = trim($request->search);

        $restaurants = Restaurant::all();

        // Categories (based on restaurant)
        $categoies = Category::when($restaurant_id, function ($q) use ($restaurant_id) {
            $q->where('restaurant_id', $restaurant_id);
        })->get();

        // Items query
        $itemsQuery = DB::table('items')
            ->join('restaurants', 'restaurants.id', '=', 'items.restaurant_id')
            ->join('categories', 'categories.id', '=', 'items.category_id')
            ->select(
                'items.*',
                'restaurants.restaurant_name',
                'categories.category_name'
            );

        // Filter by restaurant if exists
        if (!empty($restaurant_id)) {
            $itemsQuery->where('items.restaurant_id', $restaurant_id);
        }

        // Search across item, category & restaurant
        if (!empty($search)) {
            $itemsQuery->where(function ($q) use ($search) {
                $q->where('items.item_name', 'LIKE', "%{$search}%")
                    ->orWhere('categories.category_name', 'LIKE', "%{$search}%")
                    ->orWhere('restaurants.restaurant_name', 'LIKE', "%{$search}%");
            });
        }

        $items = $itemsQuery->paginate(10)->withQueryString();

        // Add-ons
        $add_ons = DB::table('add_ons')
            ->when($restaurant_id, function ($q) use ($restaurant_id) {
                $q->where('restaurant_id', $restaurant_id);
            })
            ->get();

        $selected_addons = DB::table('item_addons')->get();

        $add_ons2 = DB::table('add_ons')
            ->join('item_addons', 'item_addons.addon_id', '=', 'add_ons.id')
            ->when($restaurant_id, function ($q) use ($restaurant_id) {
                $q->where('add_ons.restaurant_id', $restaurant_id);
            })
            ->get(['add_ons.*', 'item_addons.*']);

        return view('items.index', compact(
            'restaurants',
            'categoies',
            'items',
            'add_ons',
            'selected_addons',
            'add_ons2',
            'restaurant_id'
        ));
    }
    public function store(Request $request)
    {

        $restaurant_id = session()->get('restaurant_id');

        $this->validate($request, [
            'item_name' => 'required',
            'item_name_gu' => 'required',
            'description' => 'required',
            'price' => 'required',
            'picture' => 'required',
            'picture.*' => 'mimes:doc,docx,png,jpge,jpg',
        ]);

        $data = new Item();
        if ($request->file('picture')) {
            $file = $request->file('picture');
            $filename = date('YmdHi') . $file->getClientOriginalName();
            $file->move(public_path('/items'), $filename);
            $data['picture'] = $filename;
        }

        if ($restaurant_id) {
            $data->item_name = ucwords(strtolower($request->item_name));
            $data->item_name_gu = $request->item_name_gu;
            $data->description = $request->description;
            $data->price = $request->price;
            $data->ac_price = $request->price;
            $data->category_id = $request->input('categoryId');
            $data->food_type = $request->input('foodType');
            $data->restaurant_id = $restaurant_id;
            $data->status = true;
            $data->display_in_ac = true;
            $data->display_in_non_ac = true;
            $data->fulfillment_type = $request->input('fulfillment_type');
            $data->save();
        } else {
            $data->item_name = ucwords(strtolower($request->item_name));
            $data->item_name_gu = $request->item_name_gu;
            $data->description = $request->description;
            $data->price = $request->price;
            $data->ac_price = $request->price;
            $data->category_id = $request->input('categoryId');
            $data->food_type = $request->input('foodType');
            $data->restaurant_id = $request->restaurantId;
            $data->status = true;
            $data->display_in_ac = true;
            $data->display_in_non_ac = true;
            $data->fulfillment_type = $request->input('fulfillment_type');
            $data->save();
        }


        $addonIds = $request->input('addonId');
        if (!empty($addonIds)) {
            foreach ($addonIds as $addonId) {
                DB::table('item_addons')->insert([
                    'item_id' => $data->id,
                    'addon_id' => $addonId,
                ]);
            }
        }

        // return redirect()->route('item.index');
        // return redirect()->back()->with('success', 'Item added successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Item added successfully!'
        ]);
    }
    public function update(Request $request, $id)
    {
        $restaurant_id = session()->get('restaurant_id');

        if ($restaurant_id) {
            $data = Item::find($id);
            $data->item_name = ucwords(strtolower($request->input('item_name')));
            $data->item_name_gu = $request->input('item_name_gu');
            $data->description = $request->input('description');
            $data->price = $request->input('price');
            $data->ac_price = $request->input('price');
            $data->category_id = $request->input('categoryId');
            $data->food_type = $request->input('foodType');
            $data->restaurant_id = $restaurant_id;
            $data->fulfillment_type = $request->input('fulfillment_type');

            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('/items'), $filename);
                $data['picture'] = $filename;
            }
            $data->update();
        } else {
            $data = Item::find($id);
            $data->item_name = ucwords(strtolower($request->input('item_name')));
            $data->item_name_gu = $request->input('item_name_gu');
            $data->description = $request->input('description');
            $data->price = $request->input('price');
            $data->ac_price = $request->input('ac_price');
            $data->category_id = $request->input('categoryId');
            $data->restaurant_id = $request->input('restaurantId');
            $data->food_type = $request->input('foodType');
            $data->fulfillment_type = $request->input('fulfillment_type');

            if ($request->hasFile('picture')) {
                $file = $request->file('picture');
                $filename = date('YmdHi') . $file->getClientOriginalName();
                $file->move(public_path('/items'), $filename);
                $data['picture'] = $filename;
            }
            $data->update();
        }

        // Update add-ons
        $addonIds = $request->input('addonId');

        // First remove old add-ons for this item
        DB::table('item_addons')->where('item_id', $id)->delete();

        // Then insert new add-ons
        if (!empty($addonIds)) {
            foreach ($addonIds as $addonId) {
                DB::table('item_addons')->insert([
                    'item_id' => $id,
                    'addon_id' => $addonId,
                ]);
            }
        }

        // return redirect()->route('item.index');
        // return redirect()->back()->with('success', 'Item updated successfully!');
        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully!'
        ]);
    }
    public function destroy(Item $item)
    {
        $item->delete();
        DB::table('item_addons')->where('item_id', $item->id)->delete();
        // return redirect()->route('item.index');
        return redirect()->back()->with('success', 'Item deleted successfully!');
    }
    public function admin_order(Request $request)
    {

        $newOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('orders.order_notified', false)
            ->select('orders.customer_code', 'restaurant_tables.table_name')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

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


        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count')
            ->groupBy('customer_code');

        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->where('customers.table_id', $request->id)
            ->select('customers.*', 'order_counts.count') // Select columns explicitly
            ->orderBy('order_counts.count', 'DESC')
            ->paginate(10);

        // return $orders;



        $data = DB::table('orders')
            ->selectRaw('orders.*')
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


        // $customer = DB::table('customers')
        //     ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
        //     ->where('customers.customer_code', $customerCode)
        //     ->selectRaw('restaurant_tables.table_name')
        //     ->get();

        $last = DB::table('orders')->latest()->first();
        // return $last;


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



        return view('order.index', compact('tables', 'orders', 'data', 'last', 'newOrders', 'restaurant', 'tableCount', 'tableAirCount', 'tableNonAirCount', 'availableTableCount'));
    }

    public function getOrders(Request $request)
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

        $paymentDone = DB::table('invoice_data')
            ->join('customers', 'customers.customer_code', '=', 'invoice_data.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('orders', 'orders.id', '=', 'invoice_data.order_id')
            ->where('invoice_data.status', false)
            ->select('invoice_data.customer_code', 'restaurant_tables.table_name', 'invoice_data.id', 'invoice_data.order_id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();


        $newOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('orders.order_notified', false)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        $restaurant_id = session()->get('restaurant_id');

        $orderdata = DB::table('orders')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->selectRaw('orders.*,order_print.item_data')
            ->get();

        // Get the latest orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(order_confirm) as order_confirm,MAX(checkout_with_tax) as checkout_with_tax,MAX(order_confirm_by) as order_confirm_by')
            ->groupBy('customer_code');

        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('order_print', 'order_print.customer_code', '=', 'order_counts.customer_code')
            ->where('customers.table_id', $request->table_id)
            // ->whereDate('order_counts.latest_created_at', '=', today())
            ->whereDate('order_counts.latest_created_at', '=', [$start, $end])
            ->select('customers.customer_code', 'order_counts.count', 'order_counts.status', 'order_counts.latest_created_at', 'order_counts.latest_updated_at', 'order_counts.id', 'order_print.status as order_print_status', 'order_counts.order_confirm', 'order_counts.checkout_with_tax', 'order_counts.order_confirm_by')
            ->orderBy('order_counts.latest_created_at', 'DESC')
            ->orderBy('order_counts.status', 'DESC')
            ->paginate(10)
            ->appends(request()->query()); // Add this line to maintain query parameters


        // $last = DB::table('orders')->latest()->select('orders.customer_code')->first();

        $last = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('orders.customer_code')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->first();

        $invoice_data = DB::table('invoice_data')
            ->where('table_id', $table_id)
            ->get();

        $pagination = (string) $orders->links();


        // For Complete Order

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

        $data = DB::table('orders')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->selectRaw('orders.*, order_print.item_data')
            ->get();

        return view('order.order_details', compact(
            'orders',
            'last',
            'orderdata',
            'newOrders',
            'paymentDone',
            'invoice_data',
            'pagination',
            'orders2',
            'GST_status',
            'cgst_rate',
            'sgst_rate',
            'cgst',
            'sgst',
            'total_with_tax',
            'data',
            'table_id',
            'total'
        ));
    }

    public function getLatestOrders(Request $request)
    {
        [$start, $end] = getBusinessTimeRange();

        $paymentDone = DB::table('invoice_data')
            ->join('customers', 'customers.customer_code', '=', 'invoice_data.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('orders', 'orders.id', '=', 'invoice_data.order_id')
            ->where('invoice_data.status', false)
            ->select('invoice_data.customer_code', 'restaurant_tables.table_name', 'invoice_data.id', 'invoice_data.order_id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();


        $newOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('orders.order_notified', false)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        $restaurant_id = session()->get('restaurant_id');

        $orderdata = DB::table('orders')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->selectRaw('orders.*,order_print.item_data')
            ->get();

        // Get the latest orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code, count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(order_confirm) as order_confirm,MAX(checkout_with_tax) as checkout_with_tax,MAX(order_confirm_by) as order_confirm_by')
            ->groupBy('customer_code');

        $orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('order_print', 'order_print.customer_code', '=', 'order_counts.customer_code')
            ->where('customers.table_id', $request->table_id)
            // ->whereDate('order_counts.latest_created_at', '=', today())
            ->whereDate('order_counts.latest_created_at', '=', [$start, $end])
            ->select('customers.customer_code', 'order_counts.count', 'order_counts.status', 'order_counts.latest_created_at', 'order_counts.latest_updated_at', 'order_counts.id', 'order_print.status as order_print_status', 'order_counts.order_confirm', 'order_counts.checkout_with_tax', 'order_counts.order_confirm_by')
            ->orderBy('order_counts.latest_created_at', 'DESC')
            ->orderBy('order_counts.status', 'DESC')
            ->paginate(10)
            ->appends(request()->query()); // Add this line to maintain query parameters



        // $last = DB::table('orders')->latest()->select('orders.customer_code')->first();

        $last = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('restaurant_tables.restaurant_id', $restaurant_id)
            ->select('orders.customer_code')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->first();

        $invoice_data = DB::table('invoice_data')
            ->select('invoice_data.payment_mode', 'invoice_data.order_id')
            ->get();

        // return response()->json($orders); // Return the orders as JSON
        return response()->json([
            'orders' => $orders,
            'last' => $last ? $last : (object)['customer_code' => null],
            // 'last_order' => $last_order ? $last_order : (object)['customer_code' => null],
            'orderdata' => $orderdata,
            'newOrders' => $newOrders,
            'paymentDone' => $paymentDone,
            'invoice_data' => $invoice_data ? $invoice_data : (object)['payment_mode' => null],
            'pagination' => (string) $orders->links(),
        ]);
    }

    function checkOrders()
    {
        $adminEmail = auth()->user()->email;

        $paymentDone = DB::table('invoice_data')
            ->join('customers', 'customers.customer_code', '=', 'invoice_data.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('restaurants', 'restaurants.id', '=', 'customers.restaurant_id')
            ->join('orders', 'orders.id', '=', 'invoice_data.order_id')
            ->where('invoice_data.status', false)
            ->where('restaurants.email', $adminEmail)
            ->select('invoice_data.customer_code', 'restaurant_tables.table_name', 'invoice_data.id', 'invoice_data.order_id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();


        $newOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('restaurants', 'restaurants.id', '=', 'customers.restaurant_id')
            ->where('orders.order_notified', false)
            ->where('restaurants.email', $adminEmail)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        $newTakeAwayOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->join('restaurants', 'restaurants.id', '=', 'customers.restaurant_id')
            ->where('orders.order_type', 'Take Away')
            ->where('orders.order_notified', false)
            ->where('restaurants.email', $adminEmail)
            ->select('orders.customer_code', 'orders.id', 'order_print.item_data')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        $ReadytoServed = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('restaurants', 'restaurants.id', '=', 'customers.restaurant_id')
            ->where('orders.manager_kitchen_order_notified', false)
            ->where('restaurants.email', $adminEmail)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id', 'order_print.item_data')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        return response()->json([
            'newOrders' => $newOrders,
            'paymentDone' => $paymentDone,
            'newTakeAwayOrders' => $newTakeAwayOrders,
            'ReadytoServed' => $ReadytoServed
        ]);
    }

    function checkNewOrders()
    {
        $adminEmail = auth()->user()->email;

        $newOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('restaurant_users', 'restaurant_users.restaurant_id', '=', 'customers.restaurant_id')
            ->where('orders.is_notified', 'false')
            ->where('restaurant_users.email', $adminEmail)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        $ReadytoServed = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('restaurant_users', 'restaurant_users.restaurant_id', '=', 'customers.restaurant_id')
            ->where('orders.manager_kitchen_order_notified', false)
            ->where('restaurant_users.email', $adminEmail)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id', 'order_print.item_data')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        return response()->json(['newOrders' => $newOrders, 'ReadytoServed' => $ReadytoServed]);
    }

    function checkWaiterPaymentmode()
    {
        $adminEmail = auth()->user()->email;


        $invoice_data = DB::table('orders')
            ->join('invoice_data', 'invoice_data.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'invoice_data.table_id')
            ->join('restaurants', 'restaurants.id', '=', 'invoice_data.restaurant_id')
            ->where('invoice_data.payment_mode', null)
            ->where('restaurants.email', $adminEmail)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'invoice_data.id')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        $ReadytoServedWaiter = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->join('order_print', 'order_print.customer_code', '=', 'orders.customer_code')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('restaurants', 'restaurants.id', '=', 'customers.restaurant_id')
            ->where('orders.waiter_kitchen_order_notified', false)
            ->where('restaurants.email', $adminEmail)
            ->select('orders.customer_code', 'restaurant_tables.table_name', 'orders.id', 'order_print.item_data')
            ->orderBy('orders.updated_at', 'desc') // Adjust 'created_at' to your actual timestamp column
            ->get();

        return response()->json([
            'invoice_data' => $invoice_data,
            'ReadytoServedWaiter' => $ReadytoServedWaiter
        ]);
    }

    public function updateNotified($orderId)
    {
        // Find the order by ID and update the 'is_notified' flag to true
        Order::where('id', $orderId)
            ->update(['is_notified' => 'true']);

        // Return a JSON response to indicate success
        return response()->json(['success' => true]);
    }

    public function updateOrderNotified($orderId)
    {
        // Find the order by ID and update the 'is_notified' flag to true
        Order::where('id', $orderId)
            ->update(['order_notified' => true]);

        $order = DB::table('order_print')
            ->join('orders', 'orders.customer_code', '=', 'order_print.customer_code')
            ->where('order_id', $orderId)
            ->first();

        if ($order->order_type == 'Take Away') {
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
        }

        // Return a JSON response to indicate success
        return response()->json(['success' => true]);
    }

    public function updatePrintNotified($orderId)
    {
        // Find the order by ID and update the 'is_notified' flag to true
        DB::table('invoice_data')->where('order_id', $orderId)->update(['status' => true]);

        // Return a JSON response to indicate success
        return response()->json(['success' => true]);
    }

    public function check_condition()
    {
        $now = Carbon::now();
        $restaurant_id = session()->get('restaurant_id');
        $availableTableCount = 0;

        $customer = DB::table('customers')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->join('orders', 'orders.customer_code', '=', 'customers.customer_code')
            ->where('orders.status', 'processed')
            ->select('restaurant_tables.table_name', 'orders.created_at')
            ->get()
            ->map(function ($item) use ($now) {
                $minutes = Carbon::parse($item->created_at)->diffInMinutes($now);
                $item->time_diff = $minutes . ' min ago';
                return $item;
            });

        $invoice_data = DB::table('invoice_data')
            ->join('restaurant_tables', 'restaurant_tables.id', '=', 'invoice_data.table_id')
            ->join('orders', 'orders.customer_code', '=', 'invoice_data.customer_code')
            ->where('invoice_data.status', false)
            ->select('restaurant_tables.table_name')
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

        return response()->json([
            'condition' => true,
            'customer' => $customer,
            'invoice_data' => $invoice_data,
            'availableTableCount' => $availableTableCount
        ]);
    }

    public function Invoice(Request $request)
    {

        $restaurant_id = session()->get('restaurant_id');
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

        return view('invoice.index', compact('tables'));
    }

    public function InvoiceData(Request $request, $table_id)
    {
        $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $restaurant_id = session()->get('restaurant_id');
        $invoiceData = DB::table('invoice_data')
            ->join('orders', 'orders.customer_code', '=', 'invoice_data.customer_code')
            ->where('invoice_data.restaurant_id', $restaurant_id)
            ->where('invoice_data.status', true)
            ->where('invoice_data.table_id', $table_id)
            ->whereBetween('invoice_data.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('invoice_data.*, orders.order_confirm_by')
            ->orderBy('invoice_data.created_at', 'desc')
            ->paginate(20)
            ->appends(request()->query());

        return view('invoice.invoice_data', compact('invoiceData', 'startDate', 'endDate', 'table_id'));
    }

    public function updateStatus(Request $request)
    {

        $item = Item::find($request->id);
        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item not found.']);
        }

        // Update status
        $item->status = $request->status;

        // Handle availability (allow 0 as a valid value)
        if ($request->has('availability') && $request->availability !== null) {
            $item->availability_hours = (float) $request->availability;
            $item->availability_set_at = now();
        } elseif ($request->has('availability') && is_null($request->availability)) {
            $item->availability_hours = null;
            $item->availability_set_at = null;
        }

        $item->save();

        return response()->json(['success' => true, 'message' => 'Item updated successfully.']);
    }

    public function PrintInvoiceOrder(Request $request, $id)
    {
        $restaurant_id = session()->get('restaurant_id');

        $items = DB::table('items')
            ->where('restaurant_id', $restaurant_id)
            ->get();

        // Handle invoice printing request
        $invoice_data = DB::table('invoice_data')->where('order_id', $id)
            ->join('orders', 'orders.customer_code', '=', 'invoice_data.customer_code')
            ->join('restaurants', 'restaurants.id', '=', 'invoice_data.restaurant_id')
            ->first();
            
        if (!$invoice_data) {
            return response()->json(['message' => 'No orders found'], 404);
        }

        $decodedData = json_decode($invoice_data->invoicedata, true);

        DB::table('invoice_data')->where('order_id', $id)->update(['status' => true]);

        return view('order.invoice_print', [
            'table' => ucwords($decodedData['table']) ?? 'N/A',
            'date' => $decodedData['date'] ?? $invoice_data->updated_at,
            'time' => $decodedData['time'] ?? '',
            'item_data' => $decodedData['item_data'] ?? [],
            'items' => $items,
            'cgst_rate' =>  $decodedData['cgst_rate'] ?? '',
            'sgst_rate' =>  $decodedData['sgst_rate'] ?? '',
            'cgst' =>  $decodedData['cgst'] ?? '',
            'sgst' =>  $decodedData['sgst'] ?? '',
            'total_with_tax' =>  $decodedData['total_with_tax'] ?? '',
            'payment_mode' => $decodedData['payment_mode'] ?? '',
            'total' => array_reduce($decodedData['order'] ?? [], function ($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0),
            'restaurant_id' => $invoice_data->restaurant_id,
            'order_id' => $id,
            'GST_status' => $invoice_data->GST_status,
            'coupon_code' => $invoice_data->coupon_code,
            'discount' => $invoice_data->discount,
            'logo' => $invoice_data->logo,
            'restaurant_name' => $invoice_data->restaurant_name,
            'address' => $invoice_data->address,
            'phone' => $invoice_data->phone,
            'order_type' => $invoice_data->order_type,
        ]);
    }



    public function downloadPdf(Request $request)
    {
        $invoice_data = DB::table('invoice_data')
            ->where('order_id', $request->order_id)
            ->first();

        $invoice_url =  $invoice_data->invoice_url;
        $restaurant_id = $invoice_data->restaurant_id;



        if (!$invoice_data) {
            return response()->json(['message' => 'No orders found'], 404);
        }

        // Decode JSON to an array
        $decodedData = json_decode($invoice_data->invoicedata, true);

        $restaurant = DB::table('restaurants')
            ->where('id', $restaurant_id)
            ->first();

        if ($request->applyTax == 'yes') {
            $cgst_rate = $restaurant->CGST; // e.g., 2.5%
            $sgst_rate = $restaurant->SGST; // e.g., 2.5%

            $total = $request->total; // Total amount from frontend
            $cgst = ($total * $cgst_rate) / 100;
            $sgst = ($total * $sgst_rate) / 100;
            $total_with_tax = $total + $cgst + $sgst;

            DB::table('orders')->where('id', $request->order_id)->update([
                'cgst' => $cgst,
                'sgst' => $sgst,
                'taxable_amount' => $total_with_tax,
            ]);

            $data = [
                'table'         => $decodedData['table_name'] ?? 'N/A',
                'item_data'     => $decodedData['order'] ?? [],
                'date'          => $decodedData['date'] ?? $invoice_data->updated_at,
                'time'          => $decodedData['time'] ?? '',
                'payment_mode'  => $decodedData['payment_mode'] ?? '',
                // Calculate the total so that it's available in the view:
                'total'         => array_reduce($decodedData['order'] ?? [], function ($carry, $item) {
                    return $carry + ($item['price'] * $item['quantity']);
                }, 0),
                'cgst' => $cgst,
                'sgst' => $sgst,
                'cgst_rate' => $cgst_rate,
                'sgst_rate' => $sgst_rate,
                'total_with_tax' => $total_with_tax,
            ];
        } else {

            DB::table('orders')->where('id', $request->order_id)->update([
                'cgst' => 0,
                'sgst' => 0,
                'taxable_amount' => 0,
            ]);


            $data = [
                'table'         => $decodedData['table_name'] ?? 'N/A',
                'item_data'     => $decodedData['order'] ?? [],
                'date'          => $decodedData['date'] ?? $invoice_data->updated_at,
                'time'          => $decodedData['time'] ?? '',
                'payment_mode'  => $decodedData['payment_mode'] ?? '',
                // Calculate the total so that it's available in the view:
                'total'         => array_reduce($decodedData['order'] ?? [], function ($carry, $item) {
                    return $carry + ($item['price'] * $item['quantity']);
                }, 0),
                'cgst' => '',
                'sgst' => '',
                'cgst_rate' => '',
                'sgst_rate' => '',
                'total_with_tax' => ''
            ];
        }




        $html = View::make('client.invoice', $data)->render();

        // Set Dompdf options
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');

        // Initialize Dompdf
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        // Save the PDF file in storage
        $pdfPath = public_path('invoices/invoice_' . $invoice_url . '.pdf');
        file_put_contents($pdfPath, $dompdf->output());

        return response()->json([
            'success' => true
        ]);
    }


    public function updateTaxStatus(Request $request)
    {
        $order = Order::find($request->id);
        if ($order) {
            $order->checkout_with_tax = $request->status;
            $order->save();
            return response()->json(['success' => true, 'message' => 'Status updated successfully!']);
        }
        return response()->json(['success' => false, 'message' => 'Item not found!']);
    }

    public function updateAreaStatus(Request $request)
    {
        $item = Item::find($request->id);

        if ($request->mode == 'display_in_ac') {
            $item->display_in_ac = $request->status;
            $item->save();
            return response()->json(['message' => 'AC status updated successfully!']);
        } elseif ($request->mode == 'display_in_non_ac') {
            $item->display_in_non_ac = $request->status;
            $item->save();
            return response()->json(['message' => 'Non-AC status updated successfully!']);
        }
    }
}
