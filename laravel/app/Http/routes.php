<?php
Route::get('', function () {
    return view('home');
});

Route::group(['prefix' => 'api'], function () {
	Route::post('/login', ['as' =>'login', 'uses' => 'Auth\AuthController@login']);
	Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@logout']);
});

//Route::group(['prefix' => 'api', 'before' => 'csrf',], function () {
Route::group(['prefix' => 'api', 'middleware' => 'jwt.auth'], function () {
	Route::get('/users', 'UsersController@index');
	Route::get('/users/{id}', 'UsersController@show');
	Route::post('/users', 'UsersController@store');
	Route::put('/users', 'UsersController@update');
	Route::delete('/users', 'UsersController@destroy');
	Route::get('/roles', 'RolesController@index');
});

//Route::group(['middleware' => ['auth', 'roles:admin'], 'prefix' => 'admin/api', 'before' => 'csrf',], function () {
	//	Route::resource('/files', 'DocumentsController', [
//		'except' => ['show', 'destroy', 'update', 'edit']
//	]);
//});