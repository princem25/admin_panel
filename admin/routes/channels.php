<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users', function ($user) {
    return (int) $user->id;
});

Broadcast::channel('admin.orders', function ($user) {
    return $user->role === 'admin';
});

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    return $user->orders()->where('id', $orderId)->exists() || $user->role === 'admin';
});

Broadcast::channel('store.browsing', function ($user) {
    // Authenticate and return user data for presence
    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => $user->role
    ];
});
