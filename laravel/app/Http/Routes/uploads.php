<?php
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::post('/uploads', 'UploadsController@store');
});