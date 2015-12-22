<?php

Route::post('/hits', ['middleware' => 'page:hits', 'uses' => 'HitsController@store']);

Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/hits', ['middleware' => 'perms:user.profile',
						 'uses' => 'HitsController@index']);

});

