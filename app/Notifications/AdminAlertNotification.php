<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/**
 * Generic bell notification for admins (new contact message, new complaint ...).
 */
class AdminAlertNotification extends Notification
{
    public function __construct(
        public string $message,
        public string $url,
        public string $icon = 'bi-bell',
        public ?string $detail = null,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'alert',
            'message' => $this->message,
            'total' => $this->detail,
            'url' => $this->url,
            'icon' => $this->icon,
        ];
    }
}
