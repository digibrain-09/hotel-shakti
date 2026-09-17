<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureWithinRestaurant
{
    public function handle($request, Closure $next)
    {
        $verifiedAt = session('geo_verified_at');
        $sameResto  = session('geo_restaurant_id') == session('restaurant_id');

        // Re-check every 30 minutes so a customer can't verify once and leave.
        $fresh = $verifiedAt && $sameResto && (now()->timestamp - $verifiedAt) < 1800;

        if (!$fresh) {
            if ($request->expectsJson()) {
                return response()->json([
                    'allowed' => false,
                    'message' => 'Please confirm your location to place an order.',
                ], 403);
            }
            return redirect()->route('scantable.table')
                ->with('msg', 'Please allow location access to order.');
        }

        return $next($request);
    }
}
