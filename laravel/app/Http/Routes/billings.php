<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::post('/purchases', 'PurchasesController@purchase');
});
