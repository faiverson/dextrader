<?php
Route::group(['namespace' => 'Admin', 'prefix' => 'api'], function () {
	Route::post('login', 'AuthController@login');
	Route::get('logout', 'AuthController@logout');
});
