<?php

namespace App\Http\Controllers;

use App\Mail\ContactUsMail;
use App\Mail\SendMail;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Restaurant;
use App\Models\Table;
use Facade\Ignition\Tabs\Tab;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FrontController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::all();
        return view('front.index', compact('restaurants'));
    }
    public function about()
    {
        return view('front.about');
    }
    public function chef()
    {
        return view('front.chef');
    }
    public function contact()
    {
        return view('front.contact');
    }
    public function restaurants()
    {
        return view('front.restaurants');
    }
    public function demo(Request $request)
    {
        $customers = DB::table('orders')->latest('created_at')->first();

        $tables = Table::where('id', 1)->get('restaurant_tables.id');
        $categories = Category::with('items')->where('restaurant_id', '1')->get();
        $catTab = isset($request->id) ? $request->id : $categories->first()->id;

        $cart = count(Cart::where('table_id', 1)->get());
        return view('front.demo', compact('categories', 'catTab', 'cart', 'customers'));
    }

    public function mail(Request $request)
    {
        $data = array(
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'  => $request->phone,
            'message'  => $request->message,
        );


        $to = "info@scantable.online";
        $emailSubject = 'New email from ' . $data['name'];
        // $headers = ['From' => $email, 'Reply-To' => $email, 'Content-type' => 'text/html; charset=utf-8'];

        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $message = $data['message'];

        $htmlContent = " 
                        <html> 
                        <body> 
                            <table cellspacing='0' style='border: 2px dashed #FB4314; width: 100%;'> 
                                <tr> 
                                    <th>firstName:</th><td>$name</td> 
                                </tr> 
                                <tr style='background-color: #e0e0e0;'> 
                                    <th>Email:</th><td>$email</td> 
                                </tr> 
                                <tr> 
                                    <th>Phone No.:</th><td>$phone</td> 
                                </tr> 
                                <tr> 
                                    <th>Message:</th><td>$message</td> 
                                </tr> 
                            </table> 
                        </body> 
                        </html>";

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

        mail($to, $emailSubject, $htmlContent, $headers);
        return back()->with('success', 'Thanks for contacting us!');
    }

    public function terms_conditions()
    {
        return view('front.terms');
    }

    public function privacy()
    {
        return view('front.privacy');
    }
}
