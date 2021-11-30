<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    |
    | Define fallback settings to be used in case the default is null
    |
    |
    */
    'fallback' => [
        'quickbooks' => [
            'client_id'                 => env('SETTING_FALLBACK_QUICKBOOKS_CLIENT_ID', ''),
            'client_secret'             => env('SETTING_FALLBACK_QUICKBOOKS_CLIENT_SECRET', ''),
            'environment'             => env('SETTING_FALLBACK_QUICKBOOKS_ENVIRONMENT', 'production'),
        ],
    ],

];
