<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::post('/sales/', 'PurchasesController@checkout');
});
