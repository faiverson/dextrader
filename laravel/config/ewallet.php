<?php

return [
    'merchant' => env('EWALLET_MERCHANT', 'Dex Trader'),
	'guid' => env('EWALLET_GUID', 'ddd85e30-a29e-4baf-a84c-0ac4116e3405'),
	'password' => env('EWALLET_PASSWORD', ''),
	'login' => env('EWALLET_LOGIN', 'https://ipas.globalewallet.com/MemberLogin.aspx'),
	'url' => env('EWALLET_URL', 'https://testewallet.com/eWalletWS/ws_Adapter.aspx')
];