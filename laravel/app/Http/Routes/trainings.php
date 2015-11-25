<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/training/affiliates', 'Trainings@affiliates');
	Route::get('/training/certification', 'Trainings@certification');
	Route::get('/training/pro', 'Trainings@pro');
});