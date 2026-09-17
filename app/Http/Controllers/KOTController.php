<?php

namespace App\Http\Controllers;

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
use Carbon\Carbon;

use Illuminate\Support\Facades\Cookie;
use App\Jobs\Jobname–queued;

require_once app_path('dompdf/autoload.inc.php');

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

class KOTController extends Controller
{
    public function index()
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
            ->paginate(5);

        return view('KOT.index', compact('tables'));
    }

    public function KOTData(Request $request, $table_id)
    {
        $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $restaurant_id = session()->get('restaurant_id');
        $orderKOT =  DB::table('orders')
            ->join('order_kot', 'order_kot.customer_code', '=', 'orders.customer_code')
            ->where('order_kot.restaurant_id', $restaurant_id)
            ->where('order_kot.table_id', $table_id)
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select('orders.*', 'order_kot.table_id', 'order_kot.order_id')   // only take orders data
            ->distinct()           // avoid duplicates
            ->orderBy('orders.updated_at', 'desc') 
            ->paginate(20)
            ->appends(request()->query());


        return view('KOT.kot_data', compact('orderKOT', 'startDate', 'endDate', 'table_id'));
    }

    public function KOTOrderData(Request $request, $table_id, $order_id)
    {
        $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d');

        $restaurant_id = session()->get('restaurant_id');
        $KOTorderData = DB::table('order_kot')
            ->join('orders', 'orders.customer_code', '=', 'order_kot.customer_code')
            ->where('order_kot.restaurant_id', $restaurant_id)
            ->where('order_kot.table_id', $table_id)
            ->where('order_kot.order_id', $order_id)
            // ->whereBetween('order_kot.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->selectRaw('order_kot.*, orders.order_confirm_by')
            ->paginate(20)
            ->appends(request()->query());

        return view('KOT.kot_order_data', compact('KOTorderData', 'startDate', 'endDate', 'table_id', 'order_id'));
    }
}
