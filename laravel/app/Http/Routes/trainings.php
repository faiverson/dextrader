<?php
Route::group(['middleware' => 'jwt.auth'], function () {
	Route::get('/training/affiliates', 'TrainingsController@affiliates');
	Route::get('/training/affiliates/download/{training_id}', 'TrainingsController@download')->where('training_id', '[0-9]+');

	Route::group(['middleware' => 'perms:product.ib'], function () {
		Route::get('/training/certification', 'TrainingsController@certification');
		Route::post('/training/certification', 'TrainingsController@checkpoint_certification');
		Route::get('/training/certification/download/{training_id}', 'TrainingsController@download')->where('training_id', '[0-9]+');
	});

	Route::group(['middleware' => 'perms:product.pro|product.ib.training'], function () {
		Route::get('/training/pro', 'TrainingsController@pro');
		Route::get('/training/pro/download/{training_id}', 'TrainingsController@download')->where('training_id', '[0-9]+');
	});

	Route::group(['middleware' => 'perms:trainings'], function () {
		Route::get('/training/{training_id}', 'TrainingsController@show')->where('training_id', '[0-9]+');
		Route::post('/training', 'TrainingsController@store');
		Route::put('/training/{training_id}', 'TrainingsController@update')->where('training_id', '[0-9]+');
		Route::delete('/training/{training_id}', 'TrainingsController@destroy')->where('training_id', '[0-9]+');
	});
});