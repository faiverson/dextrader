<?php
Route::post('/users/signup', 'UsersController@store');
Route::post('/users', ['middleware' => 'page:adduser',
						'uses' => 'UsersController@store']);

Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/users', 'UsersController@index');

	Route::post('/users/ewallet', 'UsersController@createEwallet');
	Route::post('/users/coming-soon', 'CommingSoonController@addUser');
	Route::get('/users/coming-soon/{product_id}', 'CommingSoonController@index')->where('product_id', '[0-9]+');

	// check if it's the user
	Route::group(['middleware' => 'is.user'], function () {
		Route::group(['namespace' => 'Auth'], function () {
			Route::post('/users/loginAs/{id}', 'AuthController@loginAs')->where('id', '[0-9]+');
		});

		Route::post('/users', 'UsersController@store');
		Route::get('/users/{id}', 'UsersController@show')->where('id', '[0-9]+');
		Route::put('/users/{id}', 'UsersController@update')->where('id', '[0-9]+');
		Route::delete('/users/{id}', 'UsersController@destroy')->where('id', '[0-9]+');

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

		Route::get('/users/{id}/commissions', 'CommissionsController@index')->where('id', '[0-9]+');
		Route::get('/users/{id}/commissions/total', 'CommissionsController@summary')->where('id', '[0-9]+');
		Route::get('/users/{id}/balance', 'CommissionsController@balance')->where('id', '[0-9]+');
		Route::get('/users/{id}/payments', 'PaymentsController@index')->where('id', '[0-9]+');

		Route::get('/users/{id}/downline', 'UsersController@downline')->where('id', '[0-9]+');
	});
});
