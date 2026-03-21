<?php

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class Greeting extends Facade
{
    /**
     * Create a new class instance.
     */
    protected static function getFacadeAccessor()
    {
        return 'greeting';
    }
}
