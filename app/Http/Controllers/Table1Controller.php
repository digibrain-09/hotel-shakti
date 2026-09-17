<?php

namespace App\Http\Controllers;

use App\Jobs\Jobname–queued;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderPrint;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\AddOn;
use App\Models\KitchenOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Models\PushSubscription;

use App\Models\RestaurantUser;
use App\Notifications\NewOrderNotification;

use App\Services\PushNotificationService;

require_once app_path('stripe/init.php');

require_once app_path('razorpay/Razorpay.php');

require_once app_path('dompdf/autoload.inc.php');

use Razorpay\Api\Api;

use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\View;

use DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;

use App\Stripe\Stripe;
use App\Stripe\PaymentIntent;
use Illuminate\Support\Facades\Cookie;


use function PHPUnit\Framework\isEmpty;

class Table1Controller extends Controller
{
    protected PushNotificationService $push;
    protected \App\Services\CouponService $coupons;

    public function __construct(PushNotificationService $push, \App\Services\CouponService $coupons)
    {
        $this->push    = $push;
        $this->coupons = $coupons;
    }
    public function index(Request $request, $table)
    {
        $restaurant = 'hotelshakti';

        $tables = DB::table('restaurant_tables')
            ->join('restaurants', 'restaurants.id', '=', 'restaurant_tables.restaurant_id')
            ->where('restaurants.restaurant_nav', $restaurant)
            ->where('restaurant_tables.table_name', $table)
            ->select('restaurant_tables.*', 'restaurants.restaurant_name')
            ->get();

        if ($tables->isNotEmpty()) {
            $tableData = $tables->first(); // Get the first matching table

            // Store values in session
            session()->put('table_id', $tableData->id);
            session()->put('restaurant_id', $tableData->restaurant_id);
            session()->put('table_name', $tableData->table_name);
            session()->put('restaurant_name', $tableData->restaurant_name);
            session()->put('seating_type', $tableData->seating_type);

            // Store logo in session
            $logo = Restaurant::where('id', $tableData->restaurant_id)->value('logo');
            session()->put('logo', $logo);

            // Redirect to clean URL without parameters
            return redirect()->route('scantable.table');
        } else {
            return view('message')->with('msg', "Sorry, we couldn't find that table in the selected restaurant.");
        }
    }

    public function showTable()
    {
        $restaurant_id = session()->get('restaurant_id');
        $tableId = session()->get('table_id');

        $seating_type = session()->get('seating_type');

        if (!$restaurant_id || !$tableId) {
            return redirect()->route('home')->with('msg', 'Invalid session data.');
        }

        $categories = Category::where('restaurant_id', $restaurant_id)
            ->whereHas('items')
            ->with('items')
            ->get();
        $customers = DB::table('orders')->latest('created_at')->first();
        $cart_count = Cart::where('table_id', $tableId)->count();
        $catTab = $categories->first()->id ?? null;


        $add_ons = DB::table('item_addons')
            ->get(['item_addons.*']);

        $cartItems = Cart::where('table_id', $tableId)->pluck('quantity', 'item_id')->toArray();

        $cart = Cart::where('table_id', $tableId)->get();

        return view('new_template.index', compact('categories', 'catTab', 'cart_count', 'customers', 'add_ons', 'cartItems', 'cart', 'seating_type'));
    }


    public function showTabs(Request $request)
    {
        $tableId = session()->get('table_id');
        $restaurant_id = session()->get('restaurant_id');
        $categories = Category::with('items')->where('restaurant_id', $restaurant_id)->get();
        $categories = Category::where('restaurant_id', $restaurant_id); // Replace this with your actual query to get categories

        return view('tabs', compact('categories'));
    }
    public function myorder()
    {
        $data = [];
        $orderId = '';
        $session_code = Cookie::get('CustomerOrderId');
        $tableId = session()->get('table_id');

        $cart_count = Cart::where('table_id', $tableId)->count();

        $data = [];
        $orderId = '';
        $checkout_with_tax = '';
        $cgst = '';
        $sgst = '';
        $cgst_rate = '';
        $sgst_rate = '';
        $total_with_tax = '';
        $GST_status = '';
        $session_code = Cookie::get('CustomerOrderId');
        $orders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('orders.customer_code', $session_code)
            ->get(['orders.*', 'customers.restaurant_id']);


        foreach ($orders as $order) {
            $data = json_decode($order->data);
            $orderId = $order->id;
            $checkout_with_tax  = $order->checkout_with_tax;
            $restaurant_id = $order->restaurant_id;
            $total_amount = $order->total_amount;
        }
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


        $items = DB::table('items')
            ->get();

        return view('new_template.myorder', compact('data', 'orders', 'orderId', 'items', 'cart_count', 'checkout_with_tax', 'cgst_rate', 'sgst_rate', 'cgst', 'sgst', 'total_with_tax', 'GST_status'));
    }
    public function myLatestOrder()
    {

        $data = [];
        $orderId = '';
        $session_code = Cookie::get('CustomerOrderId');
        $tableId = session()->get('table_id');

        $cart_count = Cart::where('table_id', $tableId)->count();

        $data = [];
        $orderId = '';
        $checkout_with_tax = '';
        $cgst = '';
        $sgst = '';
        $cgst_rate = '';
        $sgst_rate = '';
        $total_with_tax = '';
        $GST_status = '';
        $session_code = Cookie::get('CustomerOrderId');
        $payment_status = '';

        $orders = DB::table('orders')
            ->join('customers', 'customers.customer_code', '=', 'orders.customer_code')
            ->where('orders.customer_code', $session_code)
            ->get(['orders.*', 'customers.restaurant_id']);


        foreach ($orders as $order) {
            $data = json_decode($order->data);
            $orderId = $order->id;
            $checkout_with_tax  = $order->checkout_with_tax;
            $restaurant_id = $order->restaurant_id;
            $total_amount = $order->total_amount;

            $payment_status = $order->status;
        }

        // if ($payment_status == 'completed') {
        //     return response()->json(['payment_status' => 'completed']);
        // }

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


        $items = DB::table('items')
            ->get();

        $itemImageMap = [];

        foreach ($items as $item) {
            $itemImageMap[$item->id] = $item->picture;
        }

        foreach ($data as &$detail) {
            $detail->item_picture = $itemImageMap[$detail->item_id] ?? null;
        }


        // Recompute from the live order, never from cached session money.
        $lines = json_decode(json_encode($data), true) ?: [];

        $quote = $this->coupons->quote(
            session('coupon'),
            $lines,
            $GST_status ? (float) $cgst_rate : 0.0,
            $GST_status ? (float) $sgst_rate : 0.0
        );

        // A coupon that no longer qualifies gets dropped instead of silently applying.
        if (session('coupon') && $quote['coupon'] === null) {
            session()->forget('coupon');
        }

        return response()->json([
            'data'              => $data,
            'orderId'           => $orderId,
            'orders'            => $orders,
            'items'             => $items,
            'cart_count'        => $cart_count,
            'checkout_with_tax' => $checkout_with_tax,
            'cgst_rate'         => $GST_status ? $cgst_rate : 0,
            'sgst_rate'         => $GST_status ? $sgst_rate : 0,
            'GST_status'        => $GST_status,
            'sub_total'         => $quote['sub_total'],
            'coupon'            => $quote['coupon'],
            'discount'          => $quote['discount'],
            'coupon_error'      => $quote['coupon_error'],
            'grand_total'       => $quote['grand_total'],
            'cgst'              => $quote['cgst'],
            'sgst'              => $quote['sgst'],
            'total_with_tax'    => $quote['total_with_tax'],
            'payment_status'    => $payment_status,
        ]);
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
        $seating_type = session()->get('seating_type');

        $table_id = session()->get('table_id');

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

    public function cart()
    {
        $seating_type = session()->get('seating_type');

        $restaurant_id = session()->get('restaurant_id');
        $table_id = session()->get('table_id');
        $items = Item::all();
        // $show_session = session()->get('cart');
        $cart = Cart::where('table_id', $table_id)->get();

        $cart_count = Cart::where('table_id', $table_id)->count();

        $cartIds = collect($cart)->pluck('item_id');


        $suggestions = Item::whereNotIn('id', $cartIds)
            ->where('restaurant_id', $restaurant_id)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $existingCustomer = '';
        $session_code = Cookie::get('CustomerOrderId');
        if ($session_code > 0) {
            $existingCustomer = Customer::where('customer_code', $session_code)->first();
        }

        // return $existingCustomer;

        return view('new_template.cart', compact('items', 'cart', 'cart_count', 'suggestions', 'seating_type', 'existingCustomer'));
    }
    public function add_to_cart(Request $request)
    {
        // $items = Item::findOrFail($id);
        $table_id = session()->get('table_id');


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
    public function update(Request $request)
    {

        $table_id = session()->get('table_id');
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

    public function remove($id)
    {
        $cart = Cart::find($id);

        if (!$cart) {
            // Cart not found, redirect to home page
            return redirect()->route('scantable.table'); // Replace 'home' with your actual home route name
        }

        $cart->delete();

        // After deletion, check if any cart items remain for the same table_id
        $remainingCartItems = Cart::where('table_id', $cart->table_id)->exists();

        if ($remainingCartItems) {
            return redirect()->route('scantable.cart');
        } else {
            return redirect()->route('scantable.table'); // Replace 'home' with the appropriate route
        }
    }
    public function customer(Request $request)
    {
        // Check if the email already exists in the database
        $existingCustomer = Customer::where('email', $request->email)->first();

        if ($existingCustomer) {
            // Redirect back with an error message if the email already exists
            return redirect()->back()->with('message', 'The email address is already registered.');
        } else {

            $code =  "CUST" . rand(10000, 99999999);
            session()->put('session_code', $code);

            $table_id = session()->get('table_id');
            $restaurant_id = session()->get('restaurant_id');

            $table_name = session()->get('table_name');
            $restaurant_name = session()->get('restaurant_name');


            $customer = new Customer();
            $customer->customer_code = $code;
            $customer->customer_name = $request->customer_name;
            $customer->email = $request->email;
            $customer->password = $request->password;
            $customer->customer_mobile_no = $request->customer_mobile_no;
            $customer->table_id = $table_id;
            $customer->restaurant_id = $restaurant_id;
            $customer->save();
            return redirect()->route('scantable.index', [
                'restaurant' => $restaurant_name,
                'table' => $table_name,
            ]);
        }
    }

    public function payment(Request $request)
    {
        $api = new Api('rzp_test_3FofCctESjWgM5', '4VJFCnv2AWBqO0mwB4rcFmUN');
        $session_code = Cookie::get('CustomerOrderId');
        $table_name = session()->get('table_name');


        $line_items = [];


        $order = Order::findOrFail($request->order_id);

        $restaurant = DB::table('restaurants')->where('id', session('restaurant_id'))->first();
        $gstOn      = $restaurant && $restaurant->GST_status;

        $quote = $this->coupons->quote(
            session('coupon'),
            json_decode($order->data, true) ?: [],
            $gstOn ? (float) $restaurant->CGST : 0.0,
            $gstOn ? (float) $restaurant->SGST : 0.0
        );

        // ALWAYS fetch fresh data
        $items = json_decode($order->data, true);

        // Optional: filter removed items
        $items = collect($items)->filter(function ($item) {
            return isset($item['quantity']) && $item['quantity'] > 0;
        });

        $line_items = [];

        foreach ($items as $item) {
            $line_items[] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'addons' => $item['addons'] ?? null,
            ];
        }


        $OrderId = $request->order_id;
        $cgst = $request->cgst;
        $sgst = $request->sgst;
        $cgst_rate = $request->cgst_rate;
        $sgst_rate = $request->sgst_rate;
        $total_with_tax = $request->total_with_tax;

        $coupon_code = $request->coupon;
        $discount = $request->discount;

        session()->put('line_items', $line_items);

        // Calculate total amount
        $amount = round($quote['total_with_tax'] * 100);

        // return $amount;

        // Create Razorpay Order
        try {
            $order = $api->order->create([
                'amount' => $amount,
                'currency' => 'INR',
                'receipt' => 'order_rcptid_' . uniqid(),
                'notes' => [
                    'line_items' => json_encode($line_items)
                ]
            ]);

            $razorpayOrderId = $order['id'];

            // Pass order details to the view for Razorpay Checkout
            return view('client.razorpay_checkout', compact('razorpayOrderId', 'amount', 'OrderId', 'cgst', 'sgst', 'cgst_rate', 'sgst_rate', 'total_with_tax', 'table_name', 'session_code', 'coupon_code', 'discount'));
        } catch (\Exception $e) {
            return redirect()->route('scantable.PaymentCancel');
        }
        // return view('client.payment');
    }

    public function PaymentSuccess(Request $request)
    {
        $input = $request->all();
        $session_code = Cookie::get('CustomerOrderId');
        $existingCustomer = Order::where('customer_code', $session_code)->first();
        $status = 'completed';

        $line_items = session()->get('line_items', []);
        $table_name = session()->get('table_name');

        $restaurant_name = session()->get('restaurant_name');
        $restaurant_id = session()->get('restaurant_id');
        $table_id = session()->get('table_id');
        $orderId = $request->order_id;

        $order = Order::findOrFail($request->order_id);

        $restaurant = DB::table('restaurants')->where('id', session('restaurant_id'))->first();
        $gstOn      = $restaurant && $restaurant->GST_status;

        $quote = $this->coupons->quote(
            session('coupon'),
            json_decode($order->data, true) ?: [],
            $gstOn ? (float) $restaurant->CGST : 0.0,
            $gstOn ? (float) $restaurant->SGST : 0.0
        );


        // Verify the signature
        $api = new Api('rzp_test_3FofCctESjWgM5', '4VJFCnv2AWBqO0mwB4rcFmUN');
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if ($payment->status == 'captured') {

            $payment_mode = $payment->method;


            $existingCustomer->status = $status;
            $existingCustomer->update();
            Cookie::queue(Cookie::forget('CustomerOrderId'));

            // Generate PDF invoice
            $invoiceData = [
                'table' => $table_name,
                'date' => now()->format('d-m-Y'),
                'time' => date("h:i:sa"),
                'item_data' => $line_items,
                'cgst_rate'      => $gstOn ? $restaurant->CGST : 0,
                'sgst_rate'      => $gstOn ? $restaurant->SGST : 0,
                'cgst'           => $quote['cgst'],
                'sgst'           => $quote['sgst'],
                'total_with_tax' => $quote['total_with_tax'],
                'coupon_code'    => $quote['coupon'],
                'discount'       => $quote['discount'],
            ];



            $invoice_json = json_encode($invoiceData);

            $invoice = new Invoice();
            $invoice->invoice_url = $input['razorpay_payment_id'];
            $invoice->invoicedata = $invoice_json;
            $invoice->restaurant_name = $restaurant_name;
            $invoice->restaurant_id = $restaurant_id;
            $invoice->table_name = $table_name;
            $invoice->table_id = $table_id;
            $invoice->order_id = $input['order_id'];
            $invoice->customer_code = $session_code;
            $invoice->payment_mode = $payment_mode;
            $invoice->save();

            DB::table('orders')->where('id', $orderId)->update([
                'cgst'           => $quote['cgst'],
                'sgst'           => $quote['sgst'],
                'taxable_amount' => $quote['total_with_tax'],
                'coupon_code'    => $quote['coupon'],
                'discount'       => $quote['discount'],
            ]);

            $this->push->send(
                ['restaurant_admin'],
                'Time To Print The Bill',
                'Admin, The customer at ' . $table_name . ' has completed payment for order #' . $session_code . '.'
            );

            $html = View::make('client.invoice', ['order' => $order, 'restaurant' => $restaurant, 'invoiceData' => $invoiceData])->render();

            // Set Dompdf options
            $options = new Options();
            $options->set('defaultFont', 'Helvetica');

            // Initialize Dompdf
            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            // Save the PDF file in storage
            $pdfPath = public_path('invoices/invoice_' . $input['razorpay_payment_id'] . '.pdf');
            file_put_contents($pdfPath, $dompdf->output());

            session()->pull('discount', 0);
            session()->pull('coupon', null);
            session()->pull('grand_total', 0);
            session()->pull('total_with_tax', 0);
            session()->pull('cgst', null);
            session()->pull('sgst', 0);
            session()->pull('phone', 0);


            return response()->json([
                'status' => 'success',
                'redirect_url' => route('scantable.ThankYou'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'redirect_url' => route('scantable.PaymentCancel'),
            ]);
        }
    }

    public function InvoicePrint($payment_id)
    {
        $pdfPath = public_path('invoices/invoice_' . $payment_id . '.pdf');

        if (!file_exists($pdfPath)) {
            abort(404, 'Invoice not found');
        }

        $pdfUrl = asset('invoices/invoice_' . $payment_id . '.pdf'); // For displaying in iframe
        return view('client.invoice_print', compact('pdfUrl'));
    }

    public function CaseOnDelivery(Request $request)
    {
        // return $request->total_with_tax;

        $line_items = [];
        $payment_method = 'COD_' . uniqid();
        $payment_mode = 'cash';

        $order = Order::findOrFail($request->order_id);

        $restaurant = DB::table('restaurants')->where('id', session('restaurant_id'))->first();
        $gstOn      = $restaurant && $restaurant->GST_status;

        $quote = $this->coupons->quote(
            session('coupon'),
            json_decode($order->data, true) ?: [],
            $gstOn ? (float) $restaurant->CGST : 0.0,
            $gstOn ? (float) $restaurant->SGST : 0.0
        );

        // ALWAYS fetch fresh data
        $items = json_decode($order->data, true);

        // Optional: filter removed items
        $items = collect($items)->filter(function ($item) {
            return isset($item['quantity']) && $item['quantity'] > 0;
        });

        $line_items = [];

        foreach ($items as $item) {
            $line_items[] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'addons' => $item['addons'] ?? null,
            ];
        }



        $session_code = Cookie::get('CustomerOrderId');
        $existingCustomer = Order::where('customer_code', $session_code)->first();
        $status = 'completed';


        $table_name = session()->get('table_name');
        $restaurant_name = session()->get('restaurant_name');
        $restaurant_id = session()->get('restaurant_id');
        $table_id = session()->get('table_id');
        $orderId = $request->order_id;




        if (!empty($line_items)) {

            // Generate PDF invoice
            $invoiceData = [
                'table' => $table_name,
                'date' => now()->format('d-m-Y'),
                'time' => date("h:i:sa"),
                'item_data' => $line_items,
                'cgst_rate'      => $gstOn ? $restaurant->CGST : 0,
                'sgst_rate'      => $gstOn ? $restaurant->SGST : 0,
                'cgst'           => $quote['cgst'],
                'sgst'           => $quote['sgst'],
                'total_with_tax' => $quote['total_with_tax'],
                'coupon_code'    => $quote['coupon'],
                'discount'       => $quote['discount'],
            ];

            $invoice_json = json_encode($invoiceData);

            $invoice = new Invoice();
            $invoice->invoice_url = $payment_method;
            $invoice->invoicedata = $invoice_json;
            $invoice->restaurant_name = $restaurant_name;
            $invoice->restaurant_id = $restaurant_id;
            $invoice->table_name = $table_name;
            $invoice->table_id = $table_id;
            $invoice->order_id = $orderId;
            $invoice->customer_code = $session_code;
            $invoice->payment_mode = $payment_mode;
            $invoice->save();

            DB::table('orders')->where('id', $orderId)->update([
                'cgst'           => $quote['cgst'],
                'sgst'           => $quote['sgst'],
                'taxable_amount' => $quote['total_with_tax'],
                'coupon_code'    => $quote['coupon'],
                'discount'       => $quote['discount'],
            ]);

            $this->push->send(
                ['restaurant_admin'],
                'Time To Print The Bill',
                'Admin, The customer at ' . $table_name . ' has completed payment for order #' . $session_code . '.'
            );

            $html = View::make('client.invoice', ['order' => $order, 'restaurant' => $restaurant, 'invoiceData' => $invoiceData])->render();

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

            session()->pull('discount', 0);
            session()->pull('coupon', null);
            session()->pull('grand_total', 0);
            session()->pull('total_with_tax', 0);
            session()->pull('cgst', null);
            session()->pull('sgst', 0);
            session()->pull('phone', 0);

            return redirect()->route('scantable.ThankYou');
        } else {

            return redirect()->route('scantable.PaymentCancel');
        }
    }


    private function includeEscposLibrary()
    {
        // Adjust the paths based on where you placed the library in your project
        require_once app_path('Escpos/src/Mike42/Escpos/Printer.php');
        require_once app_path('Escpos/src/Mike42/Escpos/PrintConnectors/FilePrintConnector.php');
        // Add additional required files if necessary (like EscposImage.php if you're printing images)
    }

    public function data()
    {
        try {
            $this->includeEscposLibrary();
            $connector = new \Mike42\Escpos\PrintConnectors\FilePrintConnector("USB");
            $printer = new \Mike42\Escpos\Printer($connector);


            if ($printer) {
                $printer->text("Test Print from Laravel\n");
                $printer->cut();
                $printer->close();

                return response()->json(['message' => 'Print successful']);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => "Couldn't print: " . $e->getMessage()], 500);
        }
    }



    function printBill($filePath)
    {
        if (file_exists($filePath)) {
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                // Linux/macOS command for printing text file
                shell_exec('lp ' . escapeshellarg($filePath));
            } else {
                // Windows printing command (update printer name as needed)
                shell_exec('print /d:"POS80 Printer" ' . escapeshellarg($filePath));
            }
        }
    }


    public function PaymentCancel()
    {
        return redirect()->route('scantable.myorder');
    }

    public function ThankYou()
    {
        Cookie::queue(Cookie::forget('CustomerOrderId'));

        session()->pull('discount', 0);
        session()->pull('coupon', null);
        session()->pull('grand_total', 0);
        session()->pull('total_with_tax', 0);
        session()->pull('cgst', null);
        session()->pull('sgst', 0);
        session()->pull('phone', 0);

        return view('client.thankyou');
    }

    public function order(Request $request)
    {

        $session_code = Cookie::get('CustomerOrderId');
        $existingCustomer = Customer::where('customer_code', $session_code)->first();

        // store phone only if not saved before
        if (!$request->session()->has('phone')) {
            $request->session()->put('phone', $request->phone);
        }

        $table_id = session()->get('table_id');
        $restaurant_id = session()->get('restaurant_id');
        $table_name = session()->get('table_name');

        $cart = Cart::where('table_id', $table_id)->get();

        $allAddonIds = [];
        foreach ($cart as $item) {
            $addonIds = json_decode($item->addons, true); // assuming 'addons' is a JSON array
            if (is_array($addonIds)) {
                $allAddonIds = array_merge($allAddonIds, $addonIds);
            }
        }

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

        }

        // Fetch the cart items
        $cart = Cart::where('table_id', $table_id)->get();
        $status = 'processed';
        $kitchen_order_status = 'sent_to_kitchen';
        $is_notified = 'true';
        $order_confirm_by = "manager";
        $order_type = 'Dine In';
        $total_amount = $request->total_amount;



        // Check if there are existing orders for the customer
        $orders = DB::table('orders')
            ->where('orders.customer_code', $session_code)
            ->selectRaw('orders.*')
            ->get();

        $order_print = DB::table('order_print')
            ->where('order_print.customer_code', $session_code)
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


                $addons = !empty($val['addons']) ? json_decode($val['addons'], true) : [];

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
                    'print' => 'Yes',
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
                if ($order->customer_code == $session_code) {
                    $json = json_decode($order->data);

                    foreach ($cart as $number => $val) {

                        $addons = !empty($val['addons']) ? json_decode($val['addons'], true) : [];
                        $menuItem = $items->get($val['item_id']);

                        $found = false;


                        // Loop through existing order items to check if same product+addons exists
                        foreach ($json as $existingItem) {
                            if ($existingItem->item_id == $val['item_id'] && $existingItem->addons == json_encode($addons) && $existingItem->note ==  $val['note']) {
                                // Same item and addons → update quantity
                                $existingItem->quantity += $val['quantity'];
                                $found = true;
                                break;
                            }
                        }
                        unset($existingItem); // Good practice after foreach with reference

                        // If not found, add as new item
                        if (!$found) {
                            $json[] = (object) [
                                'item_id'   => $val['item_id'],
                                'name'      => $val['name'],
                                'price'     => $val['price'],
                                'quantity'  => $val['quantity'],
                                'addons'    => json_encode($addons),
                                'note'      => $val['note'],
                                'fulfillment_type' => $menuItem->fulfillment_type ?? 'kitchen',
                                'created_at' => date('Y-m-d H:i:s'),
                            ];
                        }
                    }

                    $data1 = json_encode($json);


                    $total_amount = $order->total_amount + $total_amount;

                    // Update the order data
                    $order_details =  Order::where('id', $order->id)
                        ->update(['data' => $data1, 'is_notified' => $is_notified, 'total_amount' => $total_amount, 'order_confirm' => true, 'order_notified' => false, 'kitchen_order_status' => $kitchen_order_status]);

                    foreach ($order_print as $item) {

                        $json_data_print = json_decode($item->item_data);
                        foreach ($cart as $number => $val) {
                            $addons = !empty($val['addons']) ? json_decode($val['addons'], true) : [];
                            $menuItem = $items->get($val['item_id']);

                            $json_data_print[] = [
                                'item_id' => $val['item_id'],
                                "name" => $val['name'],
                                'price' => $val['price'],
                                'quantity' => $val['quantity'],
                                'addons' =>  json_encode($addons),
                                'note' => $val['note'],
                                'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                                'print' => 'Yes',
                                'ReadytoServed' => 'No',
                                'fulfillment_type' => $menuItem->fulfillment_type ?? 'kitchen',
                                "alerted" => "No"
                            ];
                        }

                        $data2 = json_encode($json_data_print);

                        OrderPrint::where('id', $item->id)
                            ->update(['item_data' => $data2, 'status' => 0]);
                    }
                }
            }
        }


        // Dispatch jobs for each cart item
        foreach ($cart as $number => $val) {
            $details['customer_code'] = $customer_code;  // Ensure the correct customer code is used
            $details['item_id'] = $val['item_id'];
            $details['quantity'] = $val['quantity'];

            Jobname–queued::dispatch($details)->delay(now()->addYear());
        }


        $orderCode = $order->customer_code ?? 'N/A';

        $this->push->send(
            ['restaurant_admin'],
            'New Order',
            'Admin, New order ' . $orderCode . ' at ' . $table_name . '.'
        );

        $this->push->send(
            ['restaurant_manager'],
            'New Order',
            'Manager, New order ' . $orderCode . ' at ' . $table_name . '.'
        );


        // Delete cart items after order is processed
        $ids = explode(",", $table_id);
        Cart::whereIn('table_id', $ids)->delete();

        return view('client.checkout');
    }




    public function custom_login()
    {
        return view('client.login');
    }

    public function custom_register()
    {
        return view('client.register');
    }

    public function customer_check(Request $request)
    {
        $code =  "CUST" . rand(10000, 99999999);

        $currentTime = Carbon::now();
        $currentdate = $currentTime->toDateString();


        $table_id = session()->get('table_id');
        $restaurant_id = session()->get('restaurant_id');

        $table_name = session()->get('table_name');
        $restaurant_name = session()->get('restaurant_name');

        $input = $request->all();
        $customer = Customer::Where('email', $request->email)->first();
        if ($customer == null) {
            return redirect()->back()->with('message', 'The email you entered is not registered.');
        }



        $currenttime = $customer->created_at->toDateString();

        if ($input['password'] == $customer->password) {
            if ($currentdate !== $currenttime || $table_id !== $customer->table_id || $restaurant_id !== $customer->restaurant_id) {
                $data = Customer::find($customer->id);
                $data->customer_code = $code;

                // Update `table_id` only if it is different
                if ($table_id !== $customer->table_id || $restaurant_id !== $customer->restaurant_id) {
                    $data->table_id = $table_id;
                    $data->restaurant_id = $restaurant_id;
                }



                $data->update();
                session()->put('session_code', $code);
                session()->put('TabId', $table_id);

                // Redirect to the route
                return redirect()->route('scantable.index', [
                    'restaurant' => $restaurant_name,
                    'table' => $table_name,
                ]);
            } else {
                // When `currentdate == currenttime` but `table_id` matches, use the existing session code
                session()->put('session_code', $customer->customer_code);
                session()->put('TabId', $table_id);

                // Redirect to the route
                return redirect()->route('scantable.index', [
                    'restaurant' => $restaurant_name,
                    'table' => $table_name,
                ]);
            }
        } else {
            return redirect()->back()->with('message', 'Invalid Email or password.');
        }
    }

    public function tab_close()
    {
        $table_id = session()->get('table_id');
        $ids = explode(",", $table_id);
        Cart::whereIn('table_id', $ids)->delete();
    }

    public function custom_logout()
    {
        $table_id = session()->get('table_id');
        $table_name = session()->get('table_name');
        $restaurant_name = session()->get('restaurant_name');

        session()->forget('session_code');
        session()->forget('TabId');
        $ids = explode(",", $table_id);
        Cart::whereIn('table_id', $ids)->delete();

        return redirect()->route('scantable.index', [
            'restaurant' => $restaurant_name,
            'table' => $table_name,
        ]);
    }

    public function repeatOrder(Request $request)
    {
        $orderId = $request->order_id;

        // Get the old order
        $order = DB::table('orders')->where('id', $orderId)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Decode the old order items
        $orderItems = json_decode($order->data, true);
        $tableId = session()->get('table_id');

        foreach ($orderItems as $item) {
            if ($request->item_id == $item['item_id'] && $request->created_at == $item['created_at']) {
                // Add each product back to cart
                Cart::create([
                    'table_id'   => $tableId,
                    'item_id'    => $item['item_id'],
                    'name'    => $item['name'],
                    'image'    => $request->item_image,
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                    'addons'     => $item['addons'] ?? [],
                    'note'       => $item['note'] ?? '',
                ]);
            }
        }

        return redirect()->route('scantable.cart')->with('success', 'Order repeated successfully.');
    }

    protected function sendPushToManagers($order, $extra)
    {
        $tableName = $extra['table_name'] ?? 'Unknown Table';
        $orderCode = $order->customer_code ?? 'N/A';

        $restaurant_id = session()->get('restaurant_id');

        $roles = [
            'restaurant_manager' => "Manager, new order #{$orderCode} at {$tableName}.",
            'restaurant_admin'   => "Admin, new order #{$orderCode} at {$tableName}.",
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
