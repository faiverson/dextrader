<?php
Route::post('/checkout', ['middleware' => 'page:checkout',
						  'uses' => 'TransactionController@checkout']);

Route::group(['middleware' => 'jwt.auth'], function () {
	Route::group(['middleware' => 'is.user'], function () {
		Route::get('/transactions/{id}', 'TransactionController@index')->where('id', '[0-9]+');
	});
});