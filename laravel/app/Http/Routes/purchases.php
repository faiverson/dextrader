<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::group(['middleware' => 'is.user'], function () {
		Route::get('/purchases/{id}', 'PurchasesController@index')->where('id', '[0-9]+');
	});
});