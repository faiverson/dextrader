<?php

return [

    'driver' => 'eloquent',

    'model' => App\Models\User::class,

    'table' => 'users',

	'password' => [
		'email' => 'emails.password',
		'table' => 'password_resets',
		'expire' => 60,
	],

];
