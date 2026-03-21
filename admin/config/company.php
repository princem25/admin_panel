<?php

return [
    'name' => env('COMPANY_NAME', 'My Company'),
    'email' => env('COMPANY_EMAIL', 'info@company.com'),

    'address' => [
        'city' => env('COMPANY_CITY', 'Ahmedabad'),
        'country' => env('COMPANY_COUNTRY', 'India'),
    ],

    'tax' => env('COMPANY_TAX', 18),
];