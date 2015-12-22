<?php
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::post('/files/uploads', 'FilesController@store');
});