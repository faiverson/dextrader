<?php
Route::get('/', function () {
	return view('admin.home');
});

Route::group(['namespace' => 'Admin', 'prefix' => 'api'], function () {
	Route::post('login', ['uses' => 'AuthController@login']);
	Route::get('logout', ['uses' => 'AuthController@logout']);
});

Route::group(['prefix' => 'api'], function () {
    Route::group(['middleware' => 'jwt.auth'], function () {
        Route::get('/users', 'UsersController@index');
        Route::get('/users/{id}', 'UsersController@show');
        Route::post('/users', 'UsersController@store');
        Route::put('/users', 'UsersController@update');
        Route::delete('/users', 'UsersController@destroy');
        Route::get('/roles', 'RolesController@index');
    });
});
