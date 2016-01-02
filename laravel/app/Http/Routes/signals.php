<?php
Route::post('/signals', ['middleware' => 'page:signal',
						  'uses' => 'LiveSignalsController@store_by_page']);

Route::put('/signals/{signal_id}', ['middleware' => 'page:signal',
										 'uses' => 'LiveSignalsController@update_by_page'])->where('signal_id', '[0-9]+');

Route::group(['middleware' => ['jwt.auth', 'perms:signal']], function () {
	Route::get('/signals/live', 'LiveSignalsController@all_live');
	Route::get('/signals/live/{signal_id}', 'LiveSignalsController@show')->where('signal_id', '[0-9]+');
	Route::post('/signals/live', 'LiveSignalsController@store_live');
	Route::put('/signals/live/{signal_id}', 'LiveSignalsController@update_live')->where('signal_id', '[0-9]+');
	Route::delete('/signals/live/{signal_id}', 'LiveSignalsController@destroy_live')->where('signal_id', '[0-9]+');
});