<?php
Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('/providers', 'ProvidersController@index');
    Route::get('/providers/{id}', 'ProvidersController@show')->where('id', '[0-9]+');
    Route::post('/providers', 'ProvidersController@store');
    Route::put('/providers', 'ProvidersController@update');
    Route::delete('/providers/{id}', 'ProvidersController@destroy')->where('id', '[0-9]+');
});