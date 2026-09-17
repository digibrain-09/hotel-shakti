<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class PushNotificationService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => env('VAPID_SUBJECT'),
                'publicKey' => env('VAPID_PUBLIC'),
                'privateKey' => env('VAPID_PRIVATE'),
            ],
        ]);
    }

    public function send(
        array $roles,
        string $title,
        string $message,
        string $url = '/home',
        string $icon = '/images/order-icon.png'
    ): void {
        $restaurantId = session()->get('restaurant_id');

        $subs = PushSubscription::whereIn('role', $roles)
            ->where('restaurant_id', $restaurantId)
            ->get();

        foreach ($subs as $sub) {
            $data = json_decode($sub->subscription, true);

            $this->webPush->sendOneNotification(
                Subscription::create($data),
                json_encode([
                    'title' => $title,
                    'body'  => $message,
                    'icon'  => $icon,
                    'url'   => $url,
                ])
            );
        }

        foreach ($this->webPush->flush() as $report) {
            \Log::info('Push Result', [
                'success' => $report->isSuccess()
            ]);
        }
    }
}
