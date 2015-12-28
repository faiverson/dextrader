<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::group(['middleware' => 'is.user'], function () {
		Route::get('/subscriptions/{id}', 'SubscriptionsController@index')->where('id', '[0-9]+');
		Route::put('/subscriptions/{subscription_id}/users/{id}', 'SubscriptionsController@update')->where('subscription_id', '[0-9]+')->where('id', '[0-9]+');
	});
});