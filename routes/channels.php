<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast channels
|--------------------------------------------------------------------------
|
| "admin.orders" is a private channel: only a logged-in, active admin
| (admin guard) may listen to live new-order notifications.
|
*/

Broadcast::channel('admin.orders', function ($user) {
    return $user instanceof User && $user->status && $user->hasPermission('orders');
}, ['guards' => ['admin']]);
