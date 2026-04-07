<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('users', function ($user) {
    return (int) $user->id;
});

Broadcast::channel('admin.orders', function ($user) {
    return $user->role === 'admin';
});
