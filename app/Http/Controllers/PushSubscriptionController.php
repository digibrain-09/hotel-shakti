<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushSubscriptionController extends Controller
{
    public function saveSubscription(Request $request)
    {
        $userId = Auth::id(); // ensure route is protected by auth middleware
        // Accept both older raw subscription body and new { subscription, role } structure:
        $payload = $request->json()->all();

        $restaurant_id = session()->get('restaurant_id');
        
        if (isset($payload['subscription'])) {
            $subscription = $payload['subscription'];
            $role = $payload['role'] ?? session('test') ?? 'manager';
        } else {
            // Older clients may post the raw subscription JSON as the request body
            $subscription = $request->getContent();
            $role = session('test') ?? 'manager';
        }

        // save (use unique constraint per user+role to avoid duplicates)
        PushSubscription::updateOrCreate(
            ['user_id' => $userId, 'role' => $role, 'restaurant_id' => $restaurant_id],
            ['subscription' => is_string($subscription) ? $subscription : json_encode($subscription), 'role' => $role, 'restaurant_id' => $restaurant_id]
        );

        // Log::info("Push subscription saved for user {$userId} role {$role}");

        return response()->json(['success' => true]);
    }
}
