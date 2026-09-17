<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Services\GeoService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function verify(Request $request, GeoService $geo)
    {
        $data = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'accuracy'  => 'nullable|numeric',
        ]);

        $restaurantId = session('restaurant_id');
        $restaurant   = Restaurant::find($restaurantId);

        if (!$restaurant || !$restaurant->latitude) {
            session(['geo_verified_at' => now()->timestamp, 'geo_restaurant_id' => $restaurantId]);
            return response()->json(['allowed' => true, 'reason' => 'geofence_off']);
        }

        // A 2 km "accuracy" reading is a wifi/IP guess, not a GPS fix. Ask again.
        $accuracy = (float) ($data['accuracy'] ?? 0);
        if ($accuracy > 500) {
            return response()->json([
                'allowed' => false,
                'reason'  => 'low_accuracy',
                'message' => 'We could not get an accurate location. Please turn on GPS and try again.',
            ], 422);
        }

        $distance = $geo->distanceInMeters(
            (float) $data['latitude'],
            (float) $data['longitude'],
            (float) $restaurant->latitude,
            (float) $restaurant->longitude
        );

        // Small tolerance so a phone with a weak fix indoors isn't punished.
        $tolerance = min($accuracy, 75);
        $allowed   = $distance <= ($restaurant->geofence_radius + $tolerance);

        if ($allowed) {
            session([
                'geo_verified_at'   => now()->timestamp,
                'geo_restaurant_id' => $restaurantId,
            ]);
        } else {
            session()->forget('geo_verified_at');
        }

        return response()->json([
            'allowed'  => $allowed,
            'distance' => round($distance),
            'message'  => $allowed ? null : 'Ordering is only available inside the restaurant.',
        ], $allowed ? 200 : 403);
    }
}