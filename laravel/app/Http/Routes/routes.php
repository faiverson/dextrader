<?php

Route::get('/{url}', function () {
	return view('home');
})->where(['url' => '^((?!api).)*$']);

Route::group(['namespace' => 'Auth', 'prefix' => 'api'], function () {
	Route::post('/login', 'AuthController@login');
	Route::get('/logout', 'AuthController@logout');
	Route::post('/password', 'PasswordController@postEmail');
	Route::post('/password/reset', 'PasswordController@postReset');
});



//Event::listen('illuminate.query', function($query, $params)
//{
//	print $query.'<br>';
//	var_dump($params);
//	exit;
//});

//Event::listen('router.matched', function($route, $request)
//{
//	print $query.'<br>';
//	var_dump($request->path());
//});