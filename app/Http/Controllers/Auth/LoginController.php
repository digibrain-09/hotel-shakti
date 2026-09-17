<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantAdmin;
use App\Models\User;
use App\Models\RestaurantUser;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    public function login(Request $request)
    {

        $input = $request->all();
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => trans('auth.validation.username'),
            'password.required' => trans('auth.validation.password'),
            'username.exists' => trans('auth.validation.exists')
        ]);
        $remember = $request->has('remember') ? true : false;
        if ($request->type == 'admin') {

            $user = User::Where('email', $request->username)->where('role', $request->type)->first();
            if ($user == null) {
                return redirect()->route('login')
                    ->withErrors(['username' => trans('auth.validation.exists')]);
            }

            if (\Hash::check($input['password'], $user->password)) {
                Auth::login($user);
                Session::put('test', $request->type);
                return redirect()->route('home');
            } else {
                return redirect()->route('login')
                    ->withErrors(['username' => trans('auth.validation.invalid_password')]);
            }

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials)) {
                return redirect()->intended('home');
            }
        } else if ($request->type == 'restaurant_admin') {

            $user = User::Where('email', $request->username)->where('role', $request->type)->first();
            $restaurant = Restaurant::Where('email', $request->username)->first();

            // return $user->email;
            if ($restaurant) {
                if ($user == null) {
                    return redirect()->route('login')
                        ->withErrors(['username' => trans('auth.validation.exists')]);
                }


                if (\Hash::check($input['password'], $user->password)) {
                    Auth::login($user);
                    session()->put('restaurant_id', $restaurant->id);
                    session()->put('logo', $restaurant->logo);
                    session()->put('restaurant_name', $restaurant->restaurant_name);
                    session()->put('address', $restaurant->address);
                    session()->put('phoneno', $restaurant->phone);
                    Session::put('test', $request->type);
                    return redirect()->route('home');
                } else {
                    return redirect()->route('login')
                        ->withErrors(['username' => trans('auth.validation.invalid_password')]);
                }
            } else {
                return redirect()->route('login')
                    ->withErrors(['username' => trans('auth.validation.exists')]);
            }
        } else if ($request->type == 'restaurant_manager') {

            $user = User::Where('email', $request->username)->where('role', $request->type)->first();
            $restaurant_user = RestaurantUser::Where('email', $request->username)->first();

            if ($restaurant_user) {
                if ($user == null) {
                    return redirect()->route('login')
                        ->withErrors(['username' => trans('auth.validation.exists')]);
                }


                if (\Hash::check($input['password'], $user->password)) {
                    Auth::login($user);
                    session()->put('restaurant_id', $restaurant_user->restaurant_id);
                    $restaurant_data = Restaurant::findOrFail($restaurant_user->restaurant_id);
                    session()->put('logo', $restaurant_data->logo);
                    session()->put('restaurant_name', $restaurant_data->restaurant_name);
                    session()->put('address', $restaurant_data->address);
                    session()->put('phoneno', $restaurant_data->phone);
                    Session::put('test', $request->type);
                    return redirect()->route('home');
                } else {
                    return redirect()->route('login')
                        ->withErrors(['username' => trans('auth.validation.invalid_password')]);
                }
            } else {
                return redirect()->route('login')
                    ->withErrors(['username' => trans('auth.validation.exists')]);
            }
        } else if ($request->type == 'restaurant_waiter') {

            $user = User::Where('email', $request->username)->where('role', $request->type)->first();
            $restaurant_user = RestaurantUser::Where('email', $request->username)->first();

            if ($restaurant_user) {
                if ($user == null) {
                    return redirect()->route('login')
                        ->withErrors(['username' => trans('auth.validation.exists')]);
                }


                if (\Hash::check($input['password'], $user->password)) {
                    Auth::login($user);
                    session()->put('restaurant_id', $restaurant_user->restaurant_id);
                    $restaurant_data = Restaurant::findOrFail($restaurant_user->restaurant_id);
                    session()->put('logo', $restaurant_data->logo);
                    session()->put('restaurant_name', $restaurant_data->restaurant_name);
                    session()->put('address', $restaurant_data->address);
                    session()->put('phoneno', $restaurant_data->phone);
                    Session::put('test', $request->type);
                    return redirect()->route('home');
                } else {
                    return redirect()->route('login')
                        ->withErrors(['username' => trans('auth.validation.invalid_password')]);
                }
            } else {
                return redirect()->route('login')
                    ->withErrors(['username' => trans('auth.validation.exists')]);
            }
        } else if ($request->type == 'kitchen_owner') {

            $user = User::Where('email', $request->username)->where('role', $request->type)->first();
            $restaurant_user = RestaurantUser::Where('email', $request->username)->first();

            if ($restaurant_user) {
                if ($user == null) {
                    return redirect()->route('login')
                        ->withErrors(['username' => trans('auth.validation.exists')]);
                }


                if (\Hash::check($input['password'], $user->password)) {
                    Auth::login($user);
                    session()->put('restaurant_id', $restaurant_user->restaurant_id);
                    $restaurant_data = Restaurant::findOrFail($restaurant_user->restaurant_id);
                    session()->put('logo', $restaurant_data->logo);
                    session()->put('restaurant_name', $restaurant_data->restaurant_name);
                    session()->put('address', $restaurant_data->address);
                    session()->put('phoneno', $restaurant_data->phone);
                    Session::put('test', $request->type);
                    return redirect()->route('home');
                } else {
                    return redirect()->route('login')
                        ->withErrors(['username' => trans('auth.validation.invalid_password')]);
                }
            } else {
                return redirect()->route('login')
                    ->withErrors(['username' => trans('auth.validation.exists')]);
            }
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        session()->forget('restaurant_id');
        session()->forget('logo');
        session()->forget('restaurant_name');
        session()->forget('address');
        session()->forget('phoneno');
        return redirect('/login');
    }
}
