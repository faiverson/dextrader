<?php
Route::get('/', function () {
	return view('home');
});

Route::get('/abo', function () {
	return view('admin.home');
});

Route::group(['namespace' => 'Auth', 'prefix' => 'api'], function () {
	Route::post('/login', 'AuthController@login');
	Route::get('/logout', 'AuthController@logout');
});

Event::listen('illuminate.query', function($query, $params)
{
//	print $query.'<br>';
//	var_dump($params);
//	exit;
});