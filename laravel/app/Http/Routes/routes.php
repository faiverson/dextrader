<?php

Route::group(['namespace' => 'Auth', 'prefix' => 'api'], function () {
	Route::post('/login', 'AuthController@login');
	Route::post('/pages', 'AuthController@pages');
	Route::get('/logout', 'AuthController@logout');
	Route::post('/password', 'PasswordController@postEmail');
	Route::post('/password/reset', 'PasswordController@postReset');
});

//Route::get('/testing', function() {
//	$redis = \Illuminate\Support\Facades\Redis::connection();
//	$redis->set("fafa", "jojo!");
//	return $redis->get("fabi");
//});
//Event::listen('illuminate.query', function($query, $params)
//{
//	print $query.'<br>';
//	var_dump($params);
////	exit;
//});

//Event::listen('router.matched', function($route, $request)
//{
//	print $query.'<br>';
//	var_dump($request->path());
//});