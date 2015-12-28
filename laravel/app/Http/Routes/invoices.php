<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::group(['middleware' => 'is.user'], function () {
		Route::get('/invoices/{id}', 'InvoicesController@index')->where('id', '[0-9]+');
	});
});