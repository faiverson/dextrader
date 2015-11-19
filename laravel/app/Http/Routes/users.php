<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/users', 'UsersController@index');
	Route::get('/users/{id}', 'UsersController@show');
	Route::post('/users', 'UsersController@store');
	Route::put('/users', 'UsersController@update');
	Route::delete('/users', 'UsersController@destroy');
});