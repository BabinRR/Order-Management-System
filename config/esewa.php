<?php

return [

    /*
    |--------------------------------------------------------------------------
    | eSewa Payment Gateway (ePay v2)
    |--------------------------------------------------------------------------
    |
    | Sandbox defaults use EPAYTEST credentials from eSewa docs.
    | Set ESEWA_MODE=live and live merchant keys for production.
    |
    */

    'mode' => env('ESEWA_MODE', 'sandbox'),

    'merchant_code' => env('ESEWA_MERCHANT_CODE', 'EPAYTEST'),

    'secret_key' => env('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q('),

    'form_url' => env(
        'ESEWA_FORM_URL',
        env('ESEWA_MODE', 'sandbox') === 'live'
            ? 'https://epay.esewa.com.np/api/epay/main/v2/form'
            : 'https://rc-epay.esewa.com.np/api/epay/main/v2/form'
    ),

    'status_url' => env(
        'ESEWA_STATUS_URL',
        env('ESEWA_MODE', 'sandbox') === 'live'
            ? 'https://esewa.com.np/api/epay/transaction/status/'
            : 'https://rc.esewa.com.np/api/epay/transaction/status/'
    ),

];
