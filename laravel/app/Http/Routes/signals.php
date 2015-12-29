<?php
Route::post('/signals/live', ['middleware' => 'page:signal',
						  'uses' => 'LiveSignalsController@store_by_page']);

Route::put('/signals/live/{signal_id}', ['middleware' => 'page:signal',
										 'uses' => 'LiveSignalsController@update_by_page']);

Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/signals/live', 'LiveSignalsController@all');
});