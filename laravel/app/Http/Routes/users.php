<?php
Route::post('/users/signup', 'UsersController@store');

Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/users', 'UsersController@index');
	Route::post('/users', 'UsersController@store');
	Route::put('/users', 'UsersController@update');
	Route::delete('/users', 'UsersController@destroy');

	Route::post('/users/ewallet', 'UsersController@createEwallet');

	// check if it's the user
	Route::group(['middleware' => 'is.user'], function () {
		Route::get('/users/{id}', 'UsersController@show')->where('id', '[0-9]+');

		Route::get('/users/{id}/cards', 'CardsController@index')->where('id', '[0-9]+');
		Route::get('/users/{id}/cards/{card_id}', 'CardsController@show')->where('id', '[0-9]+')->where('card_id', '[0-9]+');
		Route::post('/users/{id}/cards', 'CardsController@store')->where('id', '[0-9]+');
		Route::put('/users/{id}/cards/{card_id}', 'CardsController@update')->where('id', '[0-9]+')->where('card_id', '[0-9]+');
		Route::delete('/users/{id}/cards/{card_id}', 'CardsController@destroy')->where('id', '[0-9]+')->where('card_id', '[0-9]+');

		Route::get('/users/{id}/billing-address', 'BillingAddressController@index')->where('id', '[0-9]+');
		Route::get('/users/{id}/billing-address/{address_id}', 'BillingAddressController@show')->where('id', '[0-9]+')->where('address_id', '[0-9]+');
		Route::post('/users/{id}/billing-address', 'BillingAddressController@store')->where('id', '[0-9]+');
		Route::put('/users/{id}/billing-address/{address_id}', 'BillingAddressController@update')->where('id', '[0-9]+')->where('address_id', '[0-9]+');
		Route::delete('/users/{id}/billing-address/{address_id}', 'BillingAddressController@destroy')->where('id', '[0-9]+')->where('address_id', '[0-9]+');

		Route::get('/users/{id}/invoices', 'PurchasesController@index')->where('id', '[0-9]+');

	});
});
