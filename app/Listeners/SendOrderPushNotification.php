<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderPushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\OrderPlaced  $event
     * @return void
     */
    public function handle(OrderPlaced $event)
    {
        $payload = json_encode([
            'title' => "New Order #{$event->order->id}",
            'body'  => "₹ {$event->order->total} — Tap to view",
            'url'   => route('manager.orders.show', $event->order->id)
        ]);

        $auth = [
            'VAPID' => [
                'subject'   => config('services.vapid.subject'),
                'publicKey' => config('services.vapid.public'),
                'privateKey' => config('services.vapid.private'),
            ],
        ];

        $webPush = new WebPush($auth);

        $subs = PushSubscription::all(); // limit to managers as needed

        foreach ($subs as $s) {
            $subArray = [
                'endpoint' => $s->endpoint,
                'keys' => [
                    'p256dh' => $s->p256dh,
                    'auth'   => $s->auth,
                ]
            ];

            $subscription = Subscription::create($subArray);
            $webPush->queueNotification($subscription, $payload);
        }

        foreach ($webPush->flush() as $report) {
            // $report is a MessageSentReport — you can log success/failure
            // if ($report->isSuccess()) { ... } else { ... }
        }
    }
}
