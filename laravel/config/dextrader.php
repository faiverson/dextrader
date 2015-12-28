<?php

return [

	// global limit for tables
	'limit' => 30,

	// global email for sender
	'from' => 'system@dextrader.com',

	//  commissions' porcentage
	'comms' => .50,

	//  commissions' parent porcentage
	'parent_comms' => .10,

	// encrypting numbers like CC
	'salt' => env('ENCRYPT_SALT', "CXRhW%r:m1rpq.a4'c{(-/98Q[Z^8i"),
];

