<?php
Route::post('/signals', ['middleware' => 'page:signal',
						  'uses' => 'LiveSignalsController@store_by_page']);

Route::put('/signals/{signal_id}', ['middleware' => 'page:signal',
										 'uses' => 'LiveSignalsController@update_by_page'])->where('signal_id', '[0-9]+');

Route::group(['middleware' => ['jwt.auth', 'perms:signal']], function () {
	Route::get('/signals/{product}/{signal_id}', 'LiveSignalsController@show')->where('product', '^([ib|na|fx]){2}$')->where('signal_id', '[0-9]+');
	Route::post('/signals/add/{product}', 'LiveSignalsController@store_signal')->where('product', '^([ib|na|fx]){2}$');
	Route::put('/signals/{product}/{signal_id}', 'LiveSignalsController@update_signal')->where('product', '^([ib|na|fx]){2}$')->where('signal_id', '[0-9]+');
	Route::delete('/signals/{product}/{signal_id}', 'LiveSignalsController@destroy')->where('product', '^([ib|na|fx]){2}$')->where('signal_id', '[0-9]+');
});

Route::group(['middleware' => 'jwt.auth'], function () {
	// public signal for users, the perm is on the controller for this
	Route::get('/signals/{product}', 'LiveSignalsController@all')->where('product', '^([ib|na|fx]){2}$');
});