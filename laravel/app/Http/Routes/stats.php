<?php
Route::group(['middleware' => ['jwt.auth', 'is.user']], function () {
	Route::get('/stats/{id}/marketing', 'StatsController@marketing')->where('id', '[0-9]+');

});
