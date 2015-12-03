<?php
Route::get('/abo', function () {
	return view('admin.admin');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'abo/api'], function () {
	Route::post('/login', 'AuthController@login');
	Route::get('/logout', 'AuthController@logout');
});
