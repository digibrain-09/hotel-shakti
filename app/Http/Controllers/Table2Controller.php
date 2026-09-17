<?php

namespace App\Http\Controllers;

use App\Jobs\Jobname–queued;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Order;
use App\Models\Restaurant;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

require_once app_path('stripe/init.php');

require_once app_path('razorpay/Razorpay.php');

use Razorpay\Api\Api;


use App\Stripe\Stripe;
use App\Stripe\PaymentIntent;

use function PHPUnit\Framework\isEmpty;

class Table2Controller extends Controller
{
    public function index(Request $request, $restaurant, $table)
    {
        $customers = DB::table('orders')->latest('created_at')->first();

        // $tables = Restaurant::where('restaurant_name', $restaurant)->get('restaurants.*');

        $tables = DB::table('restaurant_tables')
            ->join('restaurants', 'restaurants.id', '=', 'restaurant_tables.restaurant_id')
            ->where('restaurants.restaurant_name', $restaurant)
            ->where('restaurant_tables.table_name', $table)
            ->select('restaurant_tables.*', 'restaurants.*')
            ->get();

        foreach ($tables as $table) {
            $tableId = $table->id;
            $restaurant_id = $table->restaurant_id;

            $table_name = $table->table_name;
            $restaurant_name = $table->restaurant_name;
        }


        session()->put('table_id', $tableId);
        session()->put('restaurant_id', $restaurant_id);

        session()->put('table_name', $table_name);
        session()->put('restaurant_name', $restaurant_name);
        $categories = Category::with('items')->where('restaurant_id', $restaurant_id)->get();
        $catTab = isset($request->id) ? $request->id : $categories->first()->id;

        $cart = count(Cart::where('table_id', $tableId)->get());

        return view('client.table1', compact('categories', 'catTab', 'cart', 'customers'));
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
        $show_session = session()->get('session_code');
        $orders = DB::table('orders')
            ->where('orders.customer_code', $show_session)
            ->get(['orders.*']);
        foreach ($orders as $order) {
            $data = json_decode($order->data);
        }
        return view('client.myorder', compact('data'));
    }
    public function view($id)
    {
        $restaurant_id = session()->get('restaurant_id');
        $cart = count((is_countable(session()->get('cart')) ? session()->get('cart') : []));
        $items = Item::where('id', $id)->get();
        $categories = Category::with('items')->where('restaurant_id', $restaurant_id)->get();


        // $similarArtists = Category::whereHas('genres', function ($query) use ($genreIds) {
        //     return $query->whereIn('id', $genreIds);
        // })->whereNot('id', $artist->id)
        //     ->limit(10)
        //     ->get();


        $productsLike = DB::table('items')
            ->inRandomOrder()->take(4)->get();


        return view('client.view', compact('items', 'cart', 'categories'));
    }
    public function cart()
    {
        $table_id = session()->get('table_id');
        $items = Item::all();
        // $show_session = session()->get('cart');
        $cart = Cart::where('table_id', $table_id)->get();
        // return $show_session;
        return view('client.cart', compact('items', 'cart'));
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

        return redirect()->route('index', [
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
        return redirect()->route('cart');
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
            $table_name = session()->get('table_name');
            $restaurant_name = session()->get('restaurant_name');

            $customer = new Customer();
            $customer->customer_code = $code;
            $customer->customer_name = $request->customer_name;
            $customer->email = $request->email;
            $customer->password = $request->password;
            $customer->customer_mobile_no = $request->customer_mobile_no;
            $customer->table_id = $table_id;
            $customer->save();
            return redirect()->route('index', [
                'restaurant' => $restaurant_name,
                'table' => $table_name,
            ]);
        }
    }

    public function payment(Request $request)
    {
        // $stripe = new \Stripe\StripeClient('sk_test_51Ozw91SJVwqxTt7U4qFwfrT1etmQCbTnJRXzPiVhCi99BNyhmeXZuHwSVWJNGfI5tLaYwpgCKxXi7eevxJvZWlxN00VTbG0ytY');

        // $line_items = [];

        // foreach ($request->product_name as $index => $product_name) {
        //     $line_items[] = [
        //         'price_data' => [
        //             'currency' => 'usd',
        //             'product_data' => [
        //                 'name' => $product_name,
        //             ],
        //             'unit_amount' => $request->product_price[$index] * 100, // Convert price to cents
        //         ],
        //         'quantity' => $request->product_quantity[$index],
        //     ];
        // }

        // $response = $stripe->checkout->sessions->create([
        //     'line_items' => $line_items,
        //     'mode' => 'payment',
        //     // 'payment_method_types' => ['card'],
        //     'success_url' => route('PaymentSuccess') . '?session_id={CHECKOUT_SESSION_ID}',
        //     'cancel_url' => route('PaymentCancel'),
        // ]);
        // // dd($response);
        // if (isset($response->id) && $response->id != '') {
        //     return redirect($response->url);
        // } else {
        //     return redirect()->route('PaymentCancel');
        // }





        $api = new Api('rzp_test_3FofCctESjWgM5', '4VJFCnv2AWBqO0mwB4rcFmUN');

        $line_items = [];

        foreach ($request->product_name as $index => $product_name) {
            $line_items[] = [
                'name' => $product_name,
                'price' => $request->product_price[$index] * 100, // Convert price to paise
                'quantity' => $request->product_quantity[$index],
            ];
        }

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
            return view('client.razorpay_checkout', compact('razorpayOrderId', 'amount'));
        } catch (\Exception $e) {
            return redirect()->route('PaymentCancel');
        }
        // return view('client.payment');
    }

    public function PaymentSuccess(Request $request)
    {
        // if (isset($request->session_id)) {

        //     $stripe = new \Stripe\StripeClient('sk_test_51Ozw91SJVwqxTt7U4qFwfrT1etmQCbTnJRXzPiVhCi99BNyhmeXZuHwSVWJNGfI5tLaYwpgCKxXi7eevxJvZWlxN00VTbG0ytY');
        //     $response = $stripe->checkout->sessions->retrieve($request->session_id);
        //     //dd($response);

        //     $table_id = session()->get('table_id');
        //     $cart = Cart::where('table_id', $table_id)->get();
        //     $session_code = session()->get('session_code');

        //     $orders = DB::table('orders')
        //         ->where('orders.customer_code', $session_code)
        //         ->selectRaw('orders.*')
        //         ->get();

        //     if ($orders->isEmpty()) {
        //         $json_data = [];
        //         $order = new Order();
        //         $order->customer_code = $session_code;
        //         foreach ($cart as $number => $val) {

        //             $json_data[] = [
        //                 'item_id' => $val['item_id'],
        //                 "name" => $val['name'],
        //                 'price' => $val['price'],
        //                 'quantity' => $val['quantity'],
        //             ];
        //             $data1 = json_encode($json_data);
        //             $order->data = $data1;
        //             $order->save();
        //         }
        //     } else {
        //         foreach ($orders as $order) {
        //             if ($order->customer_code == $session_code) {
        //                 $json = json_decode($order->data);
        //                 foreach ($cart as $number => $val) {
        //                     $json[] = [
        //                         'item_id' => $val['item_id'],
        //                         "name" => $val['name'],
        //                         'price' => $val['price'],
        //                         'quantity' => $val['quantity'],
        //                     ];
        //                     $data1 = json_encode($json);

        //                     Order::where('id', $order->id)
        //                         ->update(['data' => $data1]);
        //                 }
        //             }
        //         }
        //     }

        //     foreach ($cart as $number => $val) {
        //         $details['customer_code'] = $session_code;
        //         $details['item_id'] = $val['item_id'];
        //         $details['quantity'] = $val['quantity'];

        //         Jobname–queued::dispatch($details)->delay(now()->addYear());
        //     }

        //     $ids = explode(",", $table_id);
        //     Cart::whereIn('table_id', $ids)->delete();

        //     return view('client.checkout');

        //     // session()->forget('product_name');
        //     // session()->forget('quantity');
        //     // session()->forget('price');
        // } else {
        //     return redirect()->route('PaymentCancel');
        // }



        $input = $request->all();

        // Verify the signature
        $api = new Api('rzp_test_3FofCctESjWgM5', '4VJFCnv2AWBqO0mwB4rcFmUN');
        $payment = $api->payment->fetch($input['razorpay_payment_id']);

        if ($payment->status == 'captured') {
            // Payment successful, store order data
            $table_id = session()->get('table_id');
            $cart = Cart::where('table_id', $table_id)->get();
            $session_code = session()->get('session_code');

            $orders = DB::table('orders')
                ->where('orders.customer_code', $session_code)
                ->selectRaw('orders.*')
                ->get();

            if ($orders->isEmpty()) {
                $json_data = [];
                $order = new Order();
                $order->customer_code = $session_code;
                foreach ($cart as $number => $val) {
                    $json_data[] = [
                        'item_id' => $val['item_id'],
                        "name" => $val['name'],
                        'price' => $val['price'],
                        'quantity' => $val['quantity'],
                    ];
                    $data1 = json_encode($json_data);
                    $order->data = $data1;
                    $order->save();
                }
            } else {
                foreach ($orders as $order) {
                    if ($order->customer_code == $session_code) {
                        $json = json_decode($order->data);
                        foreach ($cart as $number => $val) {
                            $json[] = [
                                'item_id' => $val['item_id'],
                                "name" => $val['name'],
                                'price' => $val['price'],
                                'quantity' => $val['quantity'],
                            ];
                            $data1 = json_encode($json);

                            Order::where('id', $order->id)
                                ->update(['data' => $data1]);
                        }
                    }
                }
            }

            foreach ($cart as $number => $val) {
                $details['customer_code'] = $session_code;
                $details['item_id'] = $val['item_id'];
                $details['quantity'] = $val['quantity'];

                Jobname–queued::dispatch($details)->delay(now()->addYear());
            }

            $ids = explode(",", $table_id);
            Cart::whereIn('table_id', $ids)->delete();

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('checkout'),
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'redirect_url' => route('PaymentCancel'),
            ]);
        }
    }

    public function PaymentCancel()
    {
        return redirect()->route('cart');
    }

    public function checkout()
    {
        return view('client.checkout');
    }
    public function createPaymentIntent(Request $request)
    {

        // Ensure you have payment_method_id in the request
        // $paymentMethodId = $request->payment_method_id;
        // if (!$paymentMethodId) {
        //     return response()->json(['error' => 'Missing payment method ID'], 400);
        // }

        // $paymentIntent = \Stripe\PaymentIntent::create([
        //     'amount' => 5000,  // Amount in cents
        //     'currency' => 'usd',
        //     'payment_method' => $paymentMethodId,
        //     'confirmation_method' => 'manual',
        //     'confirm' => true,
        // ]);

        // return response()->json([
        //     'client_secret' => $paymentIntent->client_secret
        // ]);

        // try {
        //     \Stripe\Stripe::setApiKey('sk_test_51Ozw91SJVwqxTt7U4qFwfrT1etmQCbTnJRXzPiVhCi99BNyhmeXZuHwSVWJNGfI5tLaYwpgCKxXi7eevxJvZWlxN00VTbG0ytY');


        //     $paymentIntent = \Stripe\PaymentIntent::create([
        //         'amount' => 5000, // ₹50.00 (amount is in paise for INR)
        //         'currency' => 'USD',
        //         'payment_method_types' => ['card'],
        //     ]);

        //     return response()->json([
        //         'clientSecret' => $paymentIntent->client_secret,
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json(['success' => false, 'error' => $e->getMessage()]);
        // }
    }

    public function order()
    {
        $table_id = session()->get('table_id');
        $cart = Cart::where('table_id', $table_id)->get();
        $session_code = session()->get('session_code');

        $orders = DB::table('orders')
            ->where('orders.customer_code', $session_code)
            ->selectRaw('orders.*')
            ->get();

        if ($orders->isEmpty()) {
            $json_data = [];
            $order = new Order();
            $order->customer_code = $session_code;
            foreach ($cart as $number => $val) {

                $json_data[] = [
                    'item_id' => $val['item_id'],
                    "name" => $val['name'],
                    'price' => $val['price'],
                    'quantity' => $val['quantity'],
                ];
                $data1 = json_encode($json_data);
                $order->data = $data1;
                $order->save();
            }
        } else {
            foreach ($orders as $order) {
                if ($order->customer_code == $session_code) {
                    $json = json_decode($order->data);
                    foreach ($cart as $number => $val) {
                        $json[] = [
                            'item_id' => $val['item_id'],
                            "name" => $val['name'],
                            'price' => $val['price'],
                            'quantity' => $val['quantity'],
                        ];
                        $data1 = json_encode($json);

                        Order::where('id', $order->id)
                            ->update(['data' => $data1]);
                    }
                }
            }
        }

        foreach ($cart as $number => $val) {
            $details['customer_code'] = $session_code;
            $details['item_id'] = $val['item_id'];
            $details['quantity'] = $val['quantity'];

            Jobname–queued::dispatch($details)->delay(now()->addYear());
        }

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
        $table_name = session()->get('table_name');
        $restaurant_name = session()->get('restaurant_name');

        $input = $request->all();
        $customer = Customer::Where('email', $request->email)->first();
        if ($customer == null) {
            return redirect()->back()->with('message', 'The email you entered is not registered.');
        }



        $currenttime = $customer->created_at->toDateString();

        if ($input['password'] == $customer->password) {
            if ($currentdate == $currenttime || $table_id !== $customer->table_id) {
                $data = Customer::find($customer->id);
                $data->customer_code = $code;

                // Update `table_id` only if it is different
                if ($table_id !== $customer->table_id) {
                    $data->table_id = $table_id;
                }

                $data->update();
                session()->put('session_code', $code);
                session()->put('TabId', $table_id);

                // Redirect to the route
                return redirect()->route('index', [
                    'restaurant' => $restaurant_name,
                    'table' => $table_name,
                ]);
            } else {
                // When `currentdate == currenttime` but `table_id` matches, use the existing session code
                session()->put('session_code', $customer->customer_code);
                session()->put('TabId', $table_id);

                // Redirect to the route
                return redirect()->route('index', [
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
        
        return redirect()->route('index', [
            'restaurant' => $restaurant_name,
            'table' => $table_name,
        ]);
    }
}
