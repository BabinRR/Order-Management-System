<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Khalti Payment Gateway
    |--------------------------------------------------------------------------
    |
    | Sandbox uses https://dev.khalti.com and keys from test-admin.khalti.com.
    | Set KHALTI_MODE=live and live keys for production.
    |
    */

    'mode' => env('KHALTI_MODE', 'sandbox'),

    'secret_key' => env('KHALTI_SECRET_KEY'),

    'public_key' => env('KHALTI_PUBLIC_KEY'),

    'base_url' => env(
        'KHALTI_BASE_URL',
        env('KHALTI_MODE', 'sandbox') === 'live'
            ? 'https://khalti.com/api/v2'
            : 'https://dev.khalti.com/api/v2'
    ),

];
