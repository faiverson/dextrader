<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Authentication Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the authentication driver that will be utilized.
    | This driver manages the retrieval and authentication of the users
    | attempting to get access to protected areas of your application.
    |
    | Supported: "database", "eloquent"
    |
    */

    'merchant' => env('EWALLET_MERCHANT', 'Dex Trader'),
	'guid' => env('EWALLET_GUID', 'ddd85e30-a29e-4baf-a84c-0ac4116e3405'),
	'password' => env('EWALLET_PASSWORD', 'https://testewallet.com/eWalletWS/ws_Adapter.aspx'),
	'login' => env('EWALLET_LOGIN', ''),
	'url' => env('EWALLET_URL', '')
];
