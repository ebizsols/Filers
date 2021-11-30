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
        'zohobooks' => [
            'client_id'                 => env('SETTING_FALLBACK_ZOHOBOOKS_CLIENT_ID', ''),
            'client_secret'             => env('SETTING_FALLBACK_ZOHOBOOKS_CLIENT_SECRET', ''),
        ],
    ],

];
