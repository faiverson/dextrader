<?php
Route::get('/', function () {
	return view('admin.home');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'admin/api'], function () {
	Route::post('/login', ['uses' => 'AuthController@login']);
	Route::get('/logout', ['uses' => 'AuthController@logout']);
});
