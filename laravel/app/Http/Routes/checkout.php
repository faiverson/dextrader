<?php
Route::group(['middleware' => 'page:checkout'], function () {
	Route::post('/checkout', 'TransactionController@checkout');
	Route::post('/checkout/up-down-upgrade/{id}', 'TransactionController@upgrade')->where('id', '[0-9]+');
});

Route::group(['middleware' => 'jwt.auth'], function () {
	Route::group(['middleware' => 'is.user'], function () {
		Route::post('/checkout/upgrade/{id}', 'TransactionController@upgrade')->where('id', '[0-9]+');
		Route::get('/transactions/{id}', 'TransactionController@index')->where('id', '[0-9]+');
	});

	Route::group(['middleware' => 'perms:user.update'], function () {
		Route::post('/transactions/{id}/refund', 'TransactionController@refund')->where('id', '[0-9]+');
		Route::post('/transactions/{id}/fallback', 'TransactionController@fallback')->where('id', '[0-9]+');
	});
});