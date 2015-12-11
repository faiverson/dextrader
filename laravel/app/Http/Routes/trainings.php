<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/training/affiliates', 'Trainings@affiliates');
	Route::get('/training/affiliates/download/{training_id}', 'Trainings@download')->where('training_id', '[0-9]+');

	Route::group(['middleware' => 'perms:product.ib'], function () {
		Route::get('/training/certification', 'Trainings@certification');
		Route::post('/training/certification', 'Trainings@checkpoint')->where('training_id', '[0-9]+');
		Route::get('/training/certification/download/{training_id}', 'Trainings@download')->where('training_id', '[0-9]+');
	});

	Route::group(['middleware' => 'perms:product.pro|product.ib.training'], function () {
		Route::get('/training/pro', 'Trainings@pro');
		Route::get('/training/pro/download/{training_id}', 'Trainings@download')->where('training_id', '[0-9]+');
	});
});