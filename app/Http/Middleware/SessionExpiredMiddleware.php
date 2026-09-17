<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionExpiredMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && !session()->has('last_activity')) {
            Auth::logout(); // Logout the user
            return redirect()->route('login')->with('session_expired', 'Your session has expired. Please log in again.');
        }

        session(['last_activity' => now()]); // Update last activity timestamp
        return $next($request);
    }
}
