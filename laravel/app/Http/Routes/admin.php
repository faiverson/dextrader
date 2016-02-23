<?php

Route::group(['namespace' => 'Admin', 'prefix' => 'abo'], function () {
	Route::post('/login', 'AuthController@login');
	Route::get('/logout', 'AuthController@logout');
});
