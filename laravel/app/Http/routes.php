<?php
Route::get('/', function () {
	return view('home');
});

Route::group(['prefix' => 'api'], function () {
	Route::post('/login', ['uses' => 'Auth\AuthController@login']);
	Route::get('/logout', ['uses' => 'Auth\AuthController@logout']);

	Route::group(['middleware' => 'jwt.auth'], function () {
		Route::get('/users', 'UsersController@index');
		Route::get('/users/{id}', 'UsersController@show');
		Route::post('/users', 'UsersController@store');
		Route::put('/users', 'UsersController@update');
		Route::delete('/users', 'UsersController@destroy');
		Route::get('/roles', 'RolesController@index');
	});
});

//Event::listen('illuminate.query', function($query, $params)
//{
//	var_dump($query);
//	var_dump($params);
//});