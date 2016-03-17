<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::group(['middleware' => 'is.user'], function () {
		Route::get('/invoices/{id}', 'InvoicesController@index')->where('id', '[0-9]+');
	});
});
Route::get('/invoices/{id}/download/{invoice_id}', 'InvoicesController@download')->where('id', '[0-9]+')->where('invoice_id', '[0-9]+');
