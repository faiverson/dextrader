<?php

return [

	// global limit for tables
	'limit' => 30,

	// global email for sender
	'from' => 'Maxx Fairo',

	// admin email
	'admin' => 'system@dextrader.com',

	//  commissions' porcentage
	'comms' => .40,

	//  commissions' parent porcentage
	'parent_comms' => .10,

	'holdback' => .10, /// 10 % for holdbacks

	'paid_limit' => 20, /// 10 % for holdbacks

	// encrypting numbers like CC
	'salt' => env('ENCRYPT_SALT', "CXRhW%r:m1rpq.a4'c{(-/98Q[Z^8i"),

	'unsubscribe' => env('URL') . '/unsubscribe',

	'base_url' => env('URL'),

	'email' => [
		'sender'  => 'Dex Trader',
		'logo'        => [
			'path'   => env('URL') . '/front/assets/images/logo.png',
			'width'  => '148',
			'height' => '34',
		]
	],

	'cors' => env('API_CORS', 'dextrader.com')
];

