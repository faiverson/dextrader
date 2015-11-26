<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/users', 'UsersController@index');

	Route::post('/users', 'UsersController@store');
	Route::put('/users', 'UsersController@update');
	Route::delete('/users', 'UsersController@destroy');

	// check if it's the user
	Route::group(['middleware' => 'is.user'], function () {
		Route::get('/users/{id}', 'UsersController@show')->where('id', '[0-9]+');
	});
});