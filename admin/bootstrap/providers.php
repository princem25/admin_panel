<?php

use App\Providers\AppServiceProvider;
use App\Providers\CustomServiceProvider;
use Illuminate\Broadcasting\BroadcastServiceProvider;

return [
    AppServiceProvider::class,
    CustomServiceProvider::class,
    BroadcastServiceProvider::class,
];
