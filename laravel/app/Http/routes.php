<?php
//Route::post('/login', ['as' =>'login', 'uses' => 'Auth\AuthController@postLogin']);
//Route::get('/logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);

Route::get('', function () {
    return view('home');
});

Route::group(['prefix' => 'api', 'before' => 'csrf',], function () {
	Route::get('/users', 'UsersController@index');
});

//Route::group(['middleware' => ['auth', 'roles:admin'], 'prefix' => 'admin/api', 'before' => 'csrf',], function () {
	//	Route::resource('/files', 'DocumentsController', [
//		'except' => ['show', 'destroy', 'update', 'edit']
//	]);
//});