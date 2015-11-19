<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/roles', 'RolesController@index');
	Route::get('/roles/permissions', 'RolesController@withPermissions');
});