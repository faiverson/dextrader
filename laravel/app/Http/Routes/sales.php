<?php
Route::get('/secure/{url?}', function () {
	return view('sales');
})->where(['url' => '.*']);


Route::group(['middleware' => 'jwt.auth'], function () {
	Route::post('/sales/', 'PurchasesController@checkout');
});
