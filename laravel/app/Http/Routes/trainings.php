<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/training/affiliates', 'Trainings@affiliates');
	Route::get('/training/pro', 'Trainings@pro');

	Route::group(['middleware' => 'role:IB'], function () {
		Route::get('/training/certification', 'Trainings@certification');
		Route::post('/training/certification', 'Trainings@checkpoint')->where('training_id', '[0-9]+');
	});
});