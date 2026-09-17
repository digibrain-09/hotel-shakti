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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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

class RoyalPlatterResController extends Controller
{
    public function index(Request $request, $table)
    {
        $restaurant = 'royalplatter';

        $tables = DB::table('restaurant_tables')
            ->join('restaurants', 'restaurants.id', '=', 'restaurant_tables.restaurant_id')
            ->where('restaurants.restaurant_name', $restaurant)
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

            // Store logo in session
            $logo = Restaurant::where('id', $tableData->restaurant_id)->value('logo');
            session()->put('logo', $logo);

            // Redirect to clean URL without parameters
            return redirect()->route('royalplatter.table');
        } else {
            return view('message')->with('msg', "Sorry, we couldn't find that table in the selected restaurant.");
        }
    }

    public function showTable()
    {
        $restaurant_id = session()->get('restaurant_id');
        $tableId = session()->get('table_id');

        if (!$restaurant_id || !$tableId) {
            return redirect()->route('home')->with('msg', 'Invalid session data.');
        }

        $categories = Category::with('items')->where('restaurant_id', $restaurant_id)->get();
        $customers = DB::table('orders')->latest('created_at')->first();
        $cart = Cart::where('table_id', $tableId)->count();
        $catTab = $categories->first()->id ?? null;

        return view('restaurants.royal_platter.table1', compact('categories', 'catTab', 'cart', 'customers'));
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
        $orders = DB::table('orders')
            ->where('orders.customer_code', $session_code)
            ->get(['orders.*']);
        foreach ($orders as $order) {
            $data = json_decode($order->data);
            $orderId = $order->id;
        }

        return view('restaurants.royal_platter.myorder', compact('data', 'orderId'));
    }
    public function myLatestOrder()
    {
        $data = [];
        $orderId = '';
        $checkout_with_tax = '';
        $cgst = '';
        $sgst = '';
        $cgst_rate = '';
        $sgst_rate = '';
        $total_with_tax = '';
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
        $restaurant = DB::table('restaurants')->where('id', $restaurant_id)->first();

        $cgst_rate = $restaurant->CGST;
        $sgst_rate = $restaurant->SGST;
        $total = $total_amount;

        $cgst = ($total * $cgst_rate) / 100;
        $sgst = ($total * $sgst_rate) / 100;
        $total_with_tax = $total + $cgst + $sgst;

        return response()->json([
            'data' => $data,
            'orderId' => $orderId,
            'checkout_with_tax' => $checkout_with_tax,
            'cgst_rate' => $cgst_rate,
            'sgst_rate' => $sgst_rate,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'total_with_tax' => $total_with_tax

        ]);
    }
    public function view($id)
    {
        $restaurant_id = session()->get('restaurant_id');
        $cart = count((is_countable(session()->get('cart')) ? session()->get('cart') : []));
        $items = Item::where('id', $id)->get();
        $categories = Category::with('items')->where('restaurant_id', $restaurant_id)->get();


        $productsLike = DB::table('items')
            ->inRandomOrder()->take(4)->get();


        return view('restaurants.royal_platter.view', compact('items', 'cart', 'categories'));
    }
    public function cart()
    {
        $table_id = session()->get('table_id');
        $items = Item::all();
        // $show_session = session()->get('cart');
        $cart = Cart::where('table_id', $table_id)->get();
        // return $show_session;
        return view('restaurants.royal_platter.cart', compact('items', 'cart'));
    }
    public function add_to_cart($id)
    {
        $items = Item::findOrFail($id);
        $table_id = session()->get('table_id');

        $table_name = session()->get('table_name');
        $restaurant_name = session()->get('restaurant_name');

        $cart = new Cart();
        $cart->item_id = $items->id;
        $cart->table_id = $table_id;
        $cart->name = $items->item_name;
        $cart->price = $items->price;
        $cart->quantity = '1';
        $cart->image = $items->picture;
        $cart->save();

        return redirect()->route('royalplatter.index', [
            'restaurant' => $restaurant_name,
            'table' => $table_name,
        ]);
    }
    public function update(Request $request)
    {
        if ($request->id) {
            $cart = Cart::find($request->id);
            $cart->quantity = $request->quantity;
            $cart->update();
        }
    }

    public function remove($id)
    {
        $cart = Cart::find($id);
        $cart->delete();
        return redirect()->route('royalplatter.cart');
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
            return redirect()->route('royalplatter.index', [
                'restaurant' => $restaurant_name,
                'table' => $table_name,
            ]);
        }
    }

    public function payment(Request $request)
    {
        $api = new Api('rzp_test_3FofCctESjWgM5', '4VJFCnv2AWBqO0mwB4rcFmUN');

        $line_items = [];

        foreach ($request->product_name as $index => $product_name) {
            $line_items[] = [
                'name' => $product_name,
                'price' => $request->product_price[$index], // Convert price to paise
                'quantity' => $request->product_quantity[$index],
            ];
        }

        $OrderId = $request->order_id;
        $cgst = $request->cgst;
        $sgst = $request->sgst;
        $cgst_rate = $request->cgst_rate;
        $sgst_rate = $request->sgst_rate;
        $total_with_tax = $request->total_with_tax;

        session()->put('line_items', $line_items);

        // Calculate total amount
        $amount = $request->price * 100; // Convert total price to paise

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
            return view('restaurants.royal_platter.razorpay_checkout', compact('razorpayOrderId', 'amount', 'OrderId', 'cgst', 'sgst', 'cgst_rate', 'sgst_rate', 'total_with_tax'));
        } catch (\Exception $e) {
            return redirect()->route('royalplatter.PaymentCancel');
        }
        // return view('restaurants.royal_platter.payment');
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
                'cgst_rate' => $request->cgst_rate,
                'sgst_rate' => $request->sgst_rate,
                'cgst' => $request->cgst,
                'sgst' => $request->sgst,
                'total_with_tax' => $request->total_with_tax,
                'payment_mode' => $payment_mode,
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
                'cgst' => $request->cgst,
                'sgst' => $request->sgst,
                'taxable_amount' => $request->total_with_tax,
            ]);

            $html = View::make('restaurants.royal_platter.invoice', $invoiceData)->render();

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

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('royalplatter.ThankYou'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'redirect_url' => route('royalplatter.PaymentCancel'),
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
        return view('restaurants.royal_platter.invoice_print', compact('pdfUrl'));
    }

    public function CaseOnDelivery(Request $request)
    {

        $line_items = [];
        $payment_method = 'COD_' . uniqid();
        $payment_mode = 'Cash';

        foreach ($request->product_name as $index => $product_name) {
            $line_items[] = [
                'name' => $product_name,
                'price' => $request->product_price[$index], // Convert price to paise
                'quantity' => $request->product_quantity[$index],
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
                'cgst_rate' => $request->cgst_rate,
                'sgst_rate' => $request->sgst_rate,
                'cgst' => $request->cgst,
                'sgst' => $request->sgst,
                'total_with_tax' => $request->total_with_tax,
                'payment_mode' => $payment_mode,
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
                'cgst' => $request->cgst,
                'sgst' => $request->sgst,
                'taxable_amount' => $request->total_with_tax,
            ]);

            $html = View::make('restaurants.royal_platter.invoice', $invoiceData)->render();

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

            return redirect()->route('royalplatter.ThankYou');
        } else {

            return redirect()->route('royalplatter.PaymentCancel');
        }
    }


    public function PaymentCancel()
    {
        return redirect()->route('royalplatter.myorder');
    }

    public function ThankYou()
    {
        return view('restaurants.royal_platter.thankyou');
    }


    public function order(Request $request)
    {

        $session_code = Cookie::get('CustomerOrderId');
        $existingCustomer = Customer::where('customer_code', $session_code)->first();

        $table_id = session()->get('table_id');
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
            $customer->save();

            $customer_code = $code;  // Set customer_code for new customer
        }

        // Fetch the cart items
        $cart = Cart::where('table_id', $table_id)->get();
        $status = 'processed';
        $is_notified = 'false';
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

        if ($orders->isEmpty()) {
            // If no existing orders, create a new order
            $json_data = [];
            $order = new Order();
            $order->customer_code = $customer_code; // Use the customer_code

            foreach ($cart as $number => $val) {
                $json_data[] = [
                    'item_id' => $val['item_id'],
                    "name" => $val['name'],
                    'price' => $val['price'],
                    'quantity' => $val['quantity'],
                    'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                ];

                $json_data_print[] = [
                    'item_id' => $val['item_id'],
                    "name" => $val['name'],
                    'price' => $val['price'],
                    'quantity' => $val['quantity'],
                    'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                    'print' => 'No',
                ];

                // $line_items[] =  [
                //     "name" => $val['name'],
                //     'quantity' => $val['quantity'],
                //     'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                // ];
            }

            // $invoiceData = [
            //     'order' => $line_items,
            //     'date' => now()->format('d-m-Y'),
            //     'time' => date("h:i:sa"),
            //     'table_name' => $table_name,
            // ];

            $data1 = json_encode($json_data);
            $order->data = $data1;
            $order->status = $status;
            $order->is_notified = $is_notified;
            $order->total_amount = $total_amount;
            $order->save();

            $order_id = $order->id;

            $orderprint = new OrderPrint();
            $orderprint->customer_code = $customer_code;

            $item_data = json_encode($json_data_print);

            $orderprint->item_data = $item_data;
            $orderprint->order_id = $order->id;
            $orderprint->table_id = $table_id;
            $orderprint->save();

            // return view('restaurants.royal_platter.KOT', compact('invoiceData'));
        } else {
            // If there are existing orders, update the order data
            foreach ($orders as $order) {
                if ($order->customer_code == $session_code) {
                    $json = json_decode($order->data);

                    foreach ($cart as $number => $val) {
                        $json[] = [
                            'item_id' => $val['item_id'],
                            "name" => $val['name'],
                            'price' => $val['price'],
                            'quantity' => $val['quantity'],
                            'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                        ];



                        // $line_items[] =  [
                        //     "name" => $val['name'],
                        //     'quantity' => $val['quantity'],
                        //     'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                        // ];
                    }

                    // $invoiceData = [
                    //     'order' => $line_items,
                    //     'date' => now()->format('d-m-Y'),
                    //     'time' => date("h:i:sa"),
                    //     'table_name' => $table_name,
                    // ];

                    $data1 = json_encode($json);


                    $total_amount = $order->total_amount + $total_amount;

                    // Update the order data
                    Order::where('id', $order->id)
                        ->update(['data' => $data1, 'is_notified' => $is_notified, 'total_amount' => $total_amount, 'order_confirm' => false, 'order_notified' => false]);

                    foreach ($order_print as $item) {

                        $json_data_print = json_decode($item->item_data);
                        foreach ($cart as $number => $val) {
                            $json_data_print[] = [
                                'item_id' => $val['item_id'],
                                "name" => $val['name'],
                                'price' => $val['price'],
                                'quantity' => $val['quantity'],
                                'created_at' => date('Y-m-d H:i:s'), // Add created_at timestamp here
                                'print' => 'No',
                            ];
                        }

                        $data2 = json_encode($json_data_print);

                        OrderPrint::where('id', $item->id)
                            ->update(['item_data' => $data2, 'status' => 0]);
                    }



                    // return view('restaurants.royal_platter.KOT', compact('line_items'));
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

        // Delete cart items after order is processed
        $ids = explode(",", $table_id);
        Cart::whereIn('table_id', $ids)->delete();

        return view('restaurants.royal_platter.checkout');
    }

    public function tab_close()
    {
        $table_id = session()->get('table_id');
        $ids = explode(",", $table_id);
        Cart::whereIn('table_id', $ids)->delete();
    }
}
