<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/marketing-links', 'MarketingLinks@index');
});