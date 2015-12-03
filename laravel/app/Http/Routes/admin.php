<?php
Route::get('/abo/{lala}', function () {
	return view('admin.admin');
})->where(['lala' => '.*']);

Route::group(['namespace' => 'Admin', 'prefix' => 'abo/api'], function () {
	Route::post('/login', 'AuthController@login');
	Route::get('/logout', 'AuthController@logout');
});
