<?php
Route::get('/', function () {
	return view('home');
});

Route::any('{path?}', function()
{
    return view("home");
})->where("path", ".+");

Route::group(['namespace' => 'Auth', 'prefix' => 'api'], function () {
	Route::post('/login', 'AuthController@login');
	Route::get('/logout', 'AuthController@logout');
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
//Event::listen('tymon.jwt.valid', function($user)
//{
//	var_dump($user);
//});