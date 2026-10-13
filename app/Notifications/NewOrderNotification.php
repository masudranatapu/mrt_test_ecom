<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Notification;

/**
 * Saved in the notifications table so admins see it in the bell later.
 * The live push is handled separately by the OrderPlaced event.
 */
class NewOrderNotification extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_order',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'customer' => $this->order->customer?->name,
            'total' => money($this->order->total),
            'message' => "New order {$this->order->order_number} from {$this->order->customer?->name}",
            'url' => route('admin.orders.show', $this->order),
        ];
    }
}
