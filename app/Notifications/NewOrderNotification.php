<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NewOrderNotification extends Notification
{
    use Queueable;

    public $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    // Define which channels to send
    public function via($notifiable)
    {
        return ['database', 'broadcast']; // Save in DB + Push via Pusher/Websockets
    }

    // Data for database
    public function toDatabase($notifiable)
    {
        return [
            'order_id'   => $this->order->id,
            'customer'   => $this->order->customer_name ?? 'New Customer',
            'total'      => $this->order->total_amount,
            'message'    => 'New order has been placed.',
        ];
    }

    // Data for broadcast (real-time push)
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'customer' => $this->order->customer_name ?? 'New Customer',
            'total'    => $this->order->total_amount,
            'message'  => 'New order has been placed.',
        ]);
    }
}
