<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::group(['middleware' => 'is.user'], function () {
		Route::get('/subscriptions/{id}', 'SubscriptionsController@index')->where('id', '[0-9]+');
	});
});