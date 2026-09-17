<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Category;
use App\Models\Item;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $restaurant = '';
        $tableCount = 0;
        $availableTableCount = 0;
        $restaurant_id = session()->get('restaurant_id');
        $labels = [];
        $salesData = '';
        $salesTotals = [];
        $orderTotals = [];
        $totalOrders = 0;
        $totalRevenue = 0;
        $averageOrderValue = 0;
        $restaurantCount = 0;
        $CategoryCount = 0;
        $ItemCount = 0;
        $TableCount = 0;

        $startDate = Carbon::now()->subDays(6)->toDateString();
        $endDate = Carbon::now()->toDateString();

        if ($restaurant_id) {
            $restaurant = Restaurant::where('id', $restaurant_id)->get();
            $tableCount = Table::where('restaurant_id', $restaurant_id)->count();
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


            // Create list of all dates in range
            $currentDate = Carbon::parse($startDate);
            while ($currentDate->lte(Carbon::parse($endDate))) {
                $labels[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }

            // Fetch sales data
            $salesData = DB::table('orders')
                ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
                ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('SUM(orders.taxable_amount) as total'))
                ->whereBetween('orders.created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->where('customers.restaurant_id', $restaurant_id)
                ->groupBy('date')
                ->pluck('total', 'date');

            // Fetch order count data
            $orderData = DB::table('orders')
                ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
                ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('COUNT(*) as count'))
                ->whereBetween('orders.created_at', [
                    Carbon::parse($startDate)->startOfDay(),
                    Carbon::parse($endDate)->endOfDay()
                ])
                ->where('customers.restaurant_id', $restaurant_id)
                ->groupBy('date')
                ->pluck('count', 'date');

            // Fill missing dates with 0
            foreach ($labels as $date) {
                $salesTotals[] = $salesData[$date] ?? 0;
                $orderTotals[] = $orderData[$date] ?? 0;
            }


            $orders = Order::select('orders.*')
                ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
                ->where('customers.restaurant_id', $restaurant_id)
                ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                ->get();



            $subquery = DB::table('orders')
                ->selectRaw('customer_code,count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(total_amount) as total_amount,MAX(cgst) as cgst,MAX(sgst) as sgst,MAX(taxable_amount) as taxable_amount,MAX(order_confirm_by) as order_confirm_by,MAX(data) as data')
                ->groupBy('customer_code');

            $all_orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
                $join->on('customers.customer_code', '=', 'order_counts.customer_code');
            })
                ->join('invoice_data', 'invoice_data.customer_code', '=', 'customers.customer_code')
                ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
                ->where('customers.restaurant_id', $restaurant_id)
                ->whereBetween('order_counts.latest_created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);


            $all_orders = $all_orders
                ->select(
                    'customers.customer_code',
                    'order_counts.count',
                    'order_counts.status',
                    'order_counts.latest_created_at',
                    'order_counts.latest_updated_at',
                    'order_counts.id',
                    'order_counts.total_amount',
                    'invoice_data.payment_mode',
                    'restaurant_tables.table_name',
                    'order_counts.cgst',
                    'order_counts.sgst',
                    'order_counts.taxable_amount',
                    'order_counts.order_confirm_by',
                    'order_counts.data'
                )
                ->orderBy('order_counts.latest_created_at', 'ASC')
                ->orderBy('order_counts.status', 'ASC')
                ->get();


            $totalOrders = $all_orders->count();
            $totalRevenue = $all_orders->sum('total_amount');
            $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;
        }

        $allRestaurant =  Restaurant::limit(5)->get();

        $restaurantCount = Restaurant::all()->count();
        $CategoryCount = Category::all()->count();
        $ItemCount = Item::all()->count();
        $TableCount = Table::all()->count();

        $restaurants = Restaurant::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Prepare labels & data
        $Reslabels = [];
        $Resdata   = [];

        for ($m = 1; $m <= 12; $m++) {
            $Reslabels[] = Carbon::create()->month($m)->format('M');

            $monthData = $restaurants->firstWhere('month', $m);
            $Resdata[] = $monthData ? $monthData->total : 0;
        }

        return view('admin_side_new.home', compact('restaurant', 'allRestaurant', 'tableCount', 'availableTableCount', 'labels', 'salesData', 'salesTotals', 'orderTotals', 'totalOrders', 'totalRevenue', 'averageOrderValue', 'restaurantCount', 'CategoryCount', 'ItemCount', 'TableCount', 'Reslabels', 'Resdata'));
    }
}
