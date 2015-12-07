<?php
Route::group(['middleware' => 'jwt.auth'], function () {
//	Route::get('/billing/{user_id}/cards', 'CardsController@index');
});
