<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Carbon\Carbon;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class RestaurantReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d');



        $restaurant_id = session()->get('restaurant_id');

        // Orders for summary
        $orders = Order::select('orders.*')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('customers.restaurant_id', $restaurant_id)
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();




        // Subquery for customer orders
        $subquery = DB::table('orders')
            ->selectRaw('customer_code,count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(total_amount) as total_amount,MAX(cgst) as cgst,MAX(sgst) as sgst,MAX(taxable_amount) as taxable_amount,MAX(order_confirm_by) as order_confirm_by,MAX(data) as data,MAX(order_type) as order_type')
            ->groupBy('customer_code');

        // Main orders with filters
        $all_orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('invoice_data', 'invoice_data.customer_code', '=', 'customers.customer_code')
            // ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('customers.restaurant_id', $restaurant_id)
            ->whereBetween('order_counts.latest_created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);


        // ✅ Add search filter
        // if ($request->filled('search')) {
        //     $search = $request->input('search');
        //     $all_orders->where(function ($q) use ($search) {
        //         $q->where('customers.customer_code', 'like', "%$search%")
        //             // ->orWhere('order_counts.order_confirm_by', 'like', "%$search%")
        //             ->orWhere('restaurant_tables.table_name', 'like', "%$search%");
        //     });
        // }

        // ✅ Add status filter
        if ($request->filled('status')) {
            $all_orders->where('order_counts.status', $request->input('status'));
        }

        if ($request->filled('order_type')) {
            $all_orders->where('order_counts.order_type', $request->input('order_type'));
        }

        // ✅ Add payment mode filter
        if ($request->filled('payment_mode')) {
            $all_orders->where('invoice_data.payment_mode', $request->input('payment_mode'));
        }

        if ($request->filled('order_confirm_by')) {
            $all_orders->where('order_counts.order_confirm_by', $request->input('order_confirm_by'));
        }

        // Final select
        $all_orders = $all_orders
            ->select('customers.customer_code','customers.phone', 'order_counts.count', 'order_counts.status', 'order_counts.latest_created_at', 'order_counts.latest_updated_at', 'order_counts.id', 'order_counts.total_amount', 'invoice_data.payment_mode', 'order_counts.cgst', 'order_counts.sgst', 'order_counts.taxable_amount', 'order_counts.order_confirm_by', 'order_counts.data','invoice_data.invoice_url', 'order_counts.order_type')
            ->orderBy('order_counts.latest_created_at', 'desc')
            ->orderBy('order_counts.status', 'ASC')
            ->paginate(20)
            ->appends($request->query()); // Keep filters with pagination

        // Item counts
        $itemCounts = [];
        foreach ($all_orders as $order) {
            $json = json_decode($order->data, true);
            foreach ($json as $detail) {
                $item_id = $detail['item_id'];
                $quantity = (int)$detail['quantity'];
                $price = (float)$detail['price'];

                if (isset($itemCounts[$item_id])) {
                    $itemCounts[$item_id]['count'] += $quantity;
                    $itemCounts[$item_id]['total_amount'] += $quantity * $price;
                } else {
                    $itemCounts[$item_id] = [
                        'count' => $quantity,
                        'name'  => $detail['name'],
                        'total_amount' => $quantity * $price,
                    ];
                }
            }
        }
        $topItems = collect($itemCounts)->sortByDesc('count')->take(5);


        $totalOrders = $all_orders->count();
        $totalRevenue = $all_orders->sum('total_amount');

        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        // Summary
        $grandTotal = $all_orders->sum('total_amount');
        $totalCGST = $all_orders->sum('cgst');
        $totalSGST = $all_orders->sum('sgst');
        $totalTaxableAmount = $all_orders->sum('taxable_amount');


        return view('restaurant_report.index', compact(
            'startDate',
            'endDate',
            'totalOrders',
            'totalRevenue',
            'averageOrderValue',
            'topItems',
            'all_orders',
            'orders',
            'grandTotal',
            'totalCGST',
            'totalSGST',
            'totalTaxableAmount'
        ));
    }

    public function exportReport(Request $request)
    {
        $startDate = Carbon::parse($request->input('start_date'))->format('Y-m-d');
        $endDate = Carbon::parse($request->input('end_date'))->format('Y-m-d');
        $restaurant_id = session()->get('restaurant_id');

        $orders = Order::select('orders.*')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('customers.restaurant_id', $restaurant_id)
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();



        $subquery = DB::table('orders')
            ->selectRaw('customer_code,count(customer_code) as count, MAX(status) as status, MAX(created_at) as latest_created_at, MAX(updated_at) as latest_updated_at,MAX(id) as id,MAX(total_amount) as total_amount,MAX(cgst) as cgst,MAX(sgst) as sgst,MAX(taxable_amount) as taxable_amount,MAX(order_confirm_by) as order_confirm_by,MAX(data) as data,MAX(order_type) as order_type')
            ->groupBy('customer_code');

        $all_orders = Customer::leftJoinSub($subquery, 'order_counts', function ($join) {
            $join->on('customers.customer_code', '=', 'order_counts.customer_code');
        })
            ->join('invoice_data', 'invoice_data.customer_code', '=', 'customers.customer_code')
            // ->join('restaurant_tables', 'restaurant_tables.id', '=', 'customers.table_id')
            ->where('customers.restaurant_id', $restaurant_id)
            ->whereBetween('order_counts.latest_created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        // Apply search filter
        // if ($request->filled('search')) {
        //     $search = $request->input('search');
        //     $all_orders->where(function ($q) use ($search) {
        //         $q->where('customers.customer_code', 'like', "%$search%");
        //             // ->orWhere('restaurant_tables.table_name', 'like', "%$search%");
        //     });
        // }

        // Apply status filter
        if ($request->filled('status')) {
            $all_orders->where('order_counts.status', $request->input('status'));
        }

        if ($request->filled('order_type')) {
            $all_orders->where('order_counts.order_type', $request->input('order_type'));
        }

        // Apply payment_mode filter
        if ($request->filled('payment_mode')) {
            $all_orders->where('invoice_data.payment_mode', $request->input('payment_mode'));
        }

        // Apply order_confirm_by filter
        if ($request->filled('order_confirm_by')) {
            $all_orders->where('order_counts.order_confirm_by', $request->input('order_confirm_by'));
        }

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
                // 'restaurant_tables.table_name',
                'order_counts.cgst',
                'order_counts.sgst',
                'order_counts.taxable_amount',
                // 'order_counts.order_confirm_by',
                'order_counts.data',
                'order_counts.order_type',
            )
            ->orderBy('order_counts.latest_created_at', 'ASC')
            ->orderBy('order_counts.status', 'ASC')
            ->get();

        $totalOrders = $all_orders->count();
        $totalRevenue = $all_orders->sum('total_amount');
        $averageOrderValue = $totalOrders > 0 ? round($totalRevenue / $totalOrders, 2) : 0;

        $itemCounts = [];

        foreach ($all_orders as $order) {
            $json = json_decode($order->data, true);
            foreach ($json as $detail) {
                $item_id = $detail['item_id'];
                $quantity = (int)$detail['quantity'];
                $price = (float)$detail['price']; // Convert price to float

                // Accumulate item quantity and calculate total amount
                if (isset($itemCounts[$item_id])) {
                    $itemCounts[$item_id]['count'] += $quantity;
                    $itemCounts[$item_id]['total_amount'] += $quantity * $price;
                } else {
                    $itemCounts[$item_id] = [
                        'count' => $quantity,
                        'name'  => $detail['name'], // Store name for display
                        'total_amount' => $quantity * $price, // Calculate initial total amount
                    ];
                }
            }
        }

        // Get the top 5 most popular items
        $topItems = collect($itemCounts)->sortByDesc('count')->take(5);

        $grandTotal = $all_orders->sum('total_amount');

        $totalCGST = $all_orders->sum('cgst');
        $totalSGST = $all_orders->sum('sgst');
        $totalTaxableAmount = $all_orders->sum('taxable_amount');

        $filename = "orders_report_" . date('Y-m-d') . ".csv";

        // Set headers for download
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM for proper encoding

        $output = fopen('php://output', 'w');

        // Add summary data at the top
        fputcsv($output, ['Report Summary']);
        fputcsv($output, []);
        fputcsv($output, ['Start Date', $startDate]);
        fputcsv($output, ['End  Date', $endDate]);
        fputcsv($output, []);
        fputcsv($output, ['Total Orders', $totalOrders]);
        fputcsv($output, ['Total Revenue', '₹' . number_format($totalRevenue, 2)]);
        fputcsv($output, ['Average Order Value', '₹' . number_format($averageOrderValue, 2)]);
        fputcsv($output, []); // Empty row for spacing
        // fputcsv($output, ['Most Ordered Items']);
        // fputcsv($output, []);
        // fputcsv($output, ['Item Name', 'Sold Count', 'Total Sales']);

        // Loop through orders and add data to CSV
        // foreach ($topItems as $item) {
        //     fputcsv($output, [
        //         $item['name'],        // Access using array syntax
        //         $item['count'],       // Sold quantity
        //         '₹' . number_format($item['total_amount'], 2) // Total revenue for this item
        //     ]);
        // }

        fputcsv($output, []);
        fputcsv($output, ['All Orders Data']);
        fputcsv($output, []);

        // Add table headers
        fputcsv($output, ['Customer ID','Order Type', 'Order Created', 'Order Confirm By', 'Status', 'Payment Mode', 'Total Amount', 'CGST', 'SGST', 'Total Tax', 'Taxable Amount', 'All Items Data']);

        // Store all items as JSON and include it after total amount
        foreach ($all_orders as $order) {
            $itemsData = [];
            $total = 0;


            foreach ($orders as $value) {
                if ($order->customer_code == $value->customer_code) {
                    $json = json_decode($value->data, true);
                    foreach ($json as $detail) {

                        $itemTotal = $detail['price'] * $detail['quantity'];

                        $addonTotal = 0;
                        $addonNames = [];

                        $addonData = json_decode($detail['addons'], true);

                        if (is_array($addonData)) {
                            foreach ($addonData as $addon) {
                                $addonNames[] = $addon['name']; // append names
                                $addonTotal += $addon['price'] * $detail['quantity']; // accumulate total price
                            }
                        }

                        $totalWithAddons = $itemTotal + $addonTotal;
                        $total += $totalWithAddons;

                        $note_data = $detail['note'] == null ? '-' : $detail['note'];

                        // Convert array of addon names to comma-separated string
                        $addon = empty($addonNames) ? '-' : implode(', ', $addonNames);



                        $itemsData[] = [
                            'item'      => $detail['name'],
                            'price'     => $detail['price'],
                            'quantity'  => $detail['quantity'],
                            'addons'  => $addon,
                            'note'  => $note_data,
                            'datetime'  => $value->created_at, // Order timestamp
                            'subtotal'  => $totalWithAddons
                        ];
                    }
                }
            }

            // Convert items array to JSON string
            $itemsJson = json_encode($itemsData);

            fputcsv($output, [
                // $order->table_name,
                $order->customer_code,
                $order->order_type,
                $order->latest_created_at,
                // $order->order_confirm_by,
                $order->status,
                $order->payment_mode,
                '₹' . number_format($order->total_amount, 2),
                '₹' . number_format($order->cgst, 2),
                '₹' . number_format($order->sgst, 2),
                '₹' . number_format($order->cgst + $order->sgst, 2),
                '₹' . number_format($order->taxable_amount, 2),
                $itemsJson // Store all items data in one JSON line
            ]);
        }

        fputcsv($output, []); // Empty row for spacing
        fputcsv($output, ['Grand Total', '₹' . number_format($grandTotal, 2)]);


        fputcsv($output, []); // Empty row for spacing
        fputcsv($output, ['Total CGST', '₹' . number_format($totalCGST, 2)]);


        fputcsv($output, []); // Empty row for spacing
        fputcsv($output, ['Total SGST', '₹' . number_format($totalSGST, 2)]);


        fputcsv($output, []); // Empty row for spacing
        fputcsv($output, ['Total Taxable Amount', '₹' . number_format($totalTaxableAmount, 2)]);
        fputcsv($output, []);

        // Specific Customer Code Items Details
        fputcsv($output, ['Specific Customer Code Items']);
        fputcsv($output, []);
        fputcsv($output, ['Customer ID', 'Item', 'Price', 'Quantity', 'Date & Time', 'Subtotal']);

        foreach ($all_orders as $order) {
            foreach ($orders as $value) {
                if ($order->customer_code == $value->customer_code) {
                    $json = json_decode($value->data, true);
                    foreach ($json as $detail) {
                        fputcsv($output, [
                            $order->customer_code,
                            $detail['name'],
                            '₹' . number_format($detail['price'], 2),
                            $detail['quantity'],
                            $value->created_at,
                            '₹' . number_format($detail['price'] * $detail['quantity'], 2)
                        ]);
                    }
                }
            }
        }

        fclose($output);
        exit();
    }

    public function salesChart(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');

        $startDate = $request->input('start_date') ?? Carbon::now()->subDays(6)->toDateString();
        $endDate = $request->input('end_date') ?? Carbon::now()->toDateString();

        // Create list of all dates in range
        $labels = [];
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
        $salesTotals = [];
        $orderTotals = [];

        foreach ($labels as $date) {
            $salesTotals[] = $salesData[$date] ?? 0;
            $orderTotals[] = $orderData[$date] ?? 0;
        }

        $restaurant = DB::table('restaurants')
            ->where('id', $restaurant_id)
            ->first();

        $CGST = ($restaurant && $restaurant->GST_status) ? $restaurant->CGST : 0;
        $SGST = ($restaurant && $restaurant->GST_status) ? $restaurant->SGST : 0;

        $totalGST = $CGST + $SGST;

        $revenueData = DB::table('orders')
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->join(DB::raw("
                JSON_TABLE(
                    orders.data,
                    '$[*]' COLUMNS (
                        item_id INT PATH '$.item_id',
                        quantity INT PATH '$.quantity',
                        price DECIMAL(10,2) PATH '$.price',
                        addons JSON PATH '$.addons'
                    )
                ) as oi
            "), DB::raw("1"), "=", DB::raw("1")) // cross join
            ->join('items', 'oi.item_id', '=', 'items.id')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->select(
                'categories.category_name',
                DB::raw("
                SUM(
                    (
                        (oi.quantity * oi.price) +
                        (oi.quantity * IFNULL((
                            SELECT SUM(JSON_EXTRACT(a.value, '$.price'))
                            FROM JSON_TABLE(oi.addons, '$[*]' COLUMNS (
                                value JSON PATH '$'
                            )) a
                        ), 0))
                    ) * (1 + ($totalGST / 100))
                ) as total_revenue
            ")
            )
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy('categories.category_name')
            ->orderByDesc('total_revenue')
            ->get();

        // return $revenueData;

        $categoryNames = $revenueData->pluck('category_name');
        $categoryRevenues = $revenueData->pluck('total_revenue');

        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->get();

        $topSelling = [];

        foreach ($orders as $order) {
            $items = json_decode($order->data, true);
            foreach ($items as $item) {
                $itemId = $item['item_id'];
                $quantity = (int)$item['quantity'];

                if (!isset($topSelling[$itemId])) {
                    $topSelling[$itemId] = 0;
                }
                $topSelling[$itemId] += $quantity;
            }
        }

        // Join with items table to get names
        $topItems = collect($topSelling)
            ->sortDesc()
            ->take(10)
            ->map(function ($qty, $itemId) {
                $item = DB::table('items')->where('id', $itemId)->first();
                return [
                    'item_name' => $item->item_name ?? 'Unknown',
                    'total_sold' => $qty,
                ];
            })->values();


        $itemNames = $topItems->pluck('item_name');
        $itemQuantities = $topItems->pluck('total_sold');

        // Fetch Payment Method Distribution
        $paymentMethods = DB::table('invoice_data')
            ->join('orders', 'orders.customer_code', '=', 'invoice_data.customer_code')
            ->select(
                'payment_mode',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(orders.taxable_amount) as total_sale') // change to your actual amount column
            )
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('invoice_data.restaurant_id', $restaurant_id)
            ->groupBy('payment_mode')
            ->get();

        // return $paymentMethods;

        $methodLabels = $paymentMethods->pluck('payment_mode');
        $methodCounts = $paymentMethods->pluck('count');
        $methodTotalSale = $paymentMethods->pluck('total_sale');

        $ordersByHour = DB::table('orders')
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->select(DB::raw('HOUR(orders.created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(orders.created_at)'))
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->orderBy('hour')
            ->get();

        // Format labels as AM/PM
        $hourLabels = $ordersByHour->map(function ($item) {
            return date("g A", mktime($item->hour));
        });

        $orderCounts = $ordersByHour->pluck('count');

        $totalTables = DB::table('restaurant_tables')->where('restaurant_id', $restaurant_id)->count();


        // Group orders by day with distinct table_id
        $occupancyData = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('COUNT(DISTINCT customers.table_id) as occupied_tables')
            )
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // key by date so we can lookup easily

        // Generate all dates in the range
        $allDates = [];
        $currentDate = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);

        while ($currentDate->lte($endDateObj)) {
            $allDates[] = $currentDate->toDateString();
            $currentDate->addDay();
        }

        $dates = [];
        $occupancyRates = [];

        // Fill data for each day, zero if no order
        foreach ($allDates as $day) {
            $dates[] = Carbon::parse($day)->format('M d');

            if (isset($occupancyData[$day])) {
                $occupied = $occupancyData[$day]->occupied_tables;
                $rate = round(($occupied / $totalTables) * 100, 2);
            } else {
                $rate = 0;
            }

            $occupancyRates[] = $rate;
        }

        $cancelledRejectedOrders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->select('orders.status', DB::raw('COUNT(*) as count'))
            ->whereIn('orders.status', ['processed', 'completed'])
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy('status')
            ->get();

        $cancelledRejectedCounts = $cancelledRejectedOrders->pluck('count');
        $cancelledRejectedLabels = $cancelledRejectedOrders->pluck('status');

        $dailyRevenue = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('SUM(orders.taxable_amount) as revenue')
            )
            ->where('orders.status', 'completed')
            // ->where('created_at', '>=', [$startDate])
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        // Format for Chart.js
        $revenueValues = [];
        $revenueDates = [];

        $carbonStart = Carbon::parse($startDate);
        $carbonEnd = Carbon::parse($endDate);

        while ($carbonStart->lte($carbonEnd)) {
            $date = $carbonStart->toDateString();
            $revenueDates[] = $date;
            $match = $dailyRevenue->firstWhere('date', $date);
            $revenueValues[] = $match ? round($match->revenue, 2) : 0;
            $carbonStart->addDay();
        }

        return view('restaurant_report.sales_chart', compact('labels', 'salesTotals', 'orderTotals', 'categoryNames', 'categoryRevenues', 'itemNames', 'itemQuantities', 'methodLabels', 'methodCounts', 'methodTotalSale', 'hourLabels', 'orderCounts', 'dates', 'occupancyRates', 'cancelledRejectedLabels', 'cancelledRejectedCounts', 'revenueDates', 'revenueValues'));
    }

    public function exportSalesChart(Request $request)
    {
        $restaurant_id = session()->get('restaurant_id');
        $startDate = $request->input('start_date') ?? Carbon::now()->subDays(6)->toDateString();
        $endDate = $request->input('end_date') ?? Carbon::now()->toDateString();

        $csvData = [];

        // 1. Daily Sales + Order Count
        $labels = [];
        $salesData = [];
        $orderData = [];

        $currentDate = Carbon::parse($startDate);
        while ($currentDate->lte(Carbon::parse($endDate))) {
            $labels[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        $sales = DB::table('orders')
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('ROUND(SUM(orders.taxable_amount), 2) as total'))
            ->whereBetween('orders.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy('date')
            ->pluck('total', 'date');

        $orders = DB::table('orders')
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('COUNT(*) as count'))
            ->whereBetween('orders.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy('date')
            ->pluck('count', 'date');

        $csvData[] = ['Sales Over Time'];
        $csvData[] = ['Date', 'Sales Amount', 'Order Count'];
        foreach ($labels as $date) {
            $csvData[] = [$date, $sales[$date] ?? 0, $orders[$date] ?? 0];
        }

        $csvData[] = []; // Empty row

        // 2. Top Selling Items
        $csvData[] = ['Top Selling Items'];
        $csvData[] = ['Item Name', 'Total Sold'];

        $orderList = DB::table('orders')
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->get();

        $topSelling = [];
        foreach ($orderList as $order) {
            $items = json_decode($order->data, true);
            foreach ($items as $item) {
                $itemId = $item['item_id'];
                $quantity = (int)$item['quantity'];
                if (!isset($topSelling[$itemId])) $topSelling[$itemId] = 0;
                $topSelling[$itemId] += $quantity;
            }
        }

        $topItems = collect($topSelling)->sortDesc()->take(10)->map(function ($qty, $itemId) {
            $item = DB::table('items')->find($itemId);
            return [
                'item_name' => $item->item_name ?? 'Unknown',
                'total_sold' => $qty,
            ];
        });

        foreach ($topItems as $item) {
            $csvData[] = [$item['item_name'], $item['total_sold']];
        }

        $csvData[] = [];

        // 3. Revenue by Category
        $csvData[] = ['Revenue by Category'];
        $csvData[] = ['Category Name', 'Total Revenue'];

        $restaurant = DB::table('restaurants')
            ->where('id', $restaurant_id)
            ->first();

        $CGST = ($restaurant && $restaurant->GST_status) ? $restaurant->CGST : 0;
        $SGST = ($restaurant && $restaurant->GST_status) ? $restaurant->SGST : 0;

        $totalGST = $CGST + $SGST;

        $revenueData = DB::table('orders')
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->join(DB::raw("
                JSON_TABLE(
                    orders.data,
                    '$[*]' COLUMNS (
                        item_id INT PATH '$.item_id',
                        quantity INT PATH '$.quantity',
                        price DECIMAL(10,2) PATH '$.price',
                        addons JSON PATH '$.addons'
                    )
                ) as oi
            "), DB::raw("1"), "=", DB::raw("1")) // cross join
            ->join('items', 'oi.item_id', '=', 'items.id')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->select(
                    'categories.category_name',
                    DB::raw("
                SUM(
                    (
                        (oi.quantity * oi.price) +
                        (oi.quantity * IFNULL((
                            SELECT SUM(JSON_EXTRACT(a.value, '$.price'))
                            FROM JSON_TABLE(oi.addons, '$[*]' COLUMNS (
                                value JSON PATH '$'
                            )) a
                        ), 0))
                    ) * (1 + ($totalGST / 100))
                ) as total_revenue
            ")
            )
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy('categories.category_name')
            ->orderByDesc('total_revenue')
            ->get();

        foreach ($revenueData as $row) {
            $csvData[] = [$row->category_name, round($row->total_revenue, 2)];
        }

        $csvData[] = [];

        // 4. Payment Method Distribution
        $csvData[] = ['Payment Method Distribution'];
        $csvData[] = ['Payment Method', 'Count', 'Total Sale'];

        $paymentMethods = DB::table('invoice_data')
            ->join('orders', 'orders.customer_code', '=', 'invoice_data.customer_code')
            ->select(
                'payment_mode',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(orders.taxable_amount) as total_sale') // change to your actual amount column
            )
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('invoice_data.restaurant_id', $restaurant_id)
            ->groupBy('payment_mode')
            ->get();

        foreach ($paymentMethods as $method) {
            $csvData[] = [$method->payment_mode, $method->count, round($method->total_sale, 2)];
        }

        $csvData[] = [];

        // 5. Orders by Time of Day
        $csvData[] = ['Orders by Time of Day'];
        $csvData[] = ['Hour', 'Order Count'];

        $ordersByHour = DB::table('orders')
            ->join('customers', 'orders.customer_code', '=', 'customers.customer_code')
            ->select(DB::raw('HOUR(orders.created_at) as hour'), DB::raw('COUNT(*) as count'))
            ->groupBy(DB::raw('HOUR(orders.created_at)'))
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->orderBy('hour')
            ->get();

        foreach ($ordersByHour as $order) {
            $csvData[] = [date("g A", mktime($order->hour)), $order->count];
        }

        $csvData[] = [];

        // 6. Table Occupancy Rate
        $csvData[] = ['Table Occupancy Rate'];
        $csvData[] = ['Date', 'Occupancy (%)'];

        $totalTables = DB::table('restaurant_tables')->where('restaurant_id', $restaurant_id)->count();

        // Get occupancy data only for dates with orders
        $occupancyData = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->select(
                DB::raw('DATE(orders.created_at) as date'),
                DB::raw('COUNT(DISTINCT customers.table_id) as occupied_tables')
            )
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date'); // so we can quickly find by date

        // Generate all dates in range
        $currentDate = Carbon::parse($startDate);
        $endDateObj = Carbon::parse($endDate);

        while ($currentDate->lte($endDateObj)) {
            $dateString = $currentDate->toDateString();

            if (isset($occupancyData[$dateString])) {
                $occupied = $occupancyData[$dateString]->occupied_tables;
                $rate = $totalTables ? round(($occupied / $totalTables) * 100, 2) : 0;
            } else {
                $rate = 0;
            }

            $csvData[] = [$dateString, $rate];

            $currentDate->addDay();
        }

        $csvData[] = [];

        // 7. Cancelled / Rejected
        $csvData[] = ['Order Status Distribution'];
        $csvData[] = ['Status', 'Count'];

        $cancelled = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->select('orders.status', DB::raw('COUNT(*) as count'))
            ->whereIn('orders.status', ['processed', 'completed'])
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy('status')
            ->get();

        foreach ($cancelled as $item) {
            $csvData[] = [$item->status, $item->count];
        }

        $csvData[] = [];

        // 8. Daily Revenue
        $csvData[] = ['Daily Revenue'];
        $csvData[] = ['Date', 'Revenue'];

        $dailyRevenue = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('SUM(orders.taxable_amount) as revenue'))
            ->where('orders.status', 'completed')
            ->whereBetween('orders.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('customers.restaurant_id', $restaurant_id)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->get();

        $carbonStart = Carbon::parse($startDate);
        $carbonEnd = Carbon::parse($endDate);

        while ($carbonStart->lte($carbonEnd)) {
            $date = $carbonStart->toDateString();
            $match = $dailyRevenue->firstWhere('date', $date);
            $csvData[] = [$date, $match ? round($match->revenue, 2) : 0];
            $carbonStart->addDay();
        }

        // Send as CSV
        $filename = 'report_chart_' . now()->format('Ymd_His') . '.csv';
        $handle = fopen('php://temp', 'r+');
        foreach ($csvData as $line) {
            fputcsv($handle, $line);
        }
        rewind($handle);

        return Response::stream(function () use ($handle) {
            fpassthru($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}
