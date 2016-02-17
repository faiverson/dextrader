<?php

Route::get('/testimonials', 'TestimonialsController@index');

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('/testimonials/{id}', 'TestimonialsController@show')->where('id', '[0-9]+');
    Route::post('/testimonials', 'TestimonialsController@store');
    Route::put('/testimonials', 'TestimonialsController@update');
    Route::delete('/testimonials/{id}', 'TestimonialsController@destroy')->where('id', '[0-9]+');
});