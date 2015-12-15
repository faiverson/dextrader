<?php
Route::group([], function () {
    Route::get('/countries/{q}', 'CountriesController@countries');
    Route::get('/countries/{country_code}/cities/{q}', 'CountriesController@cities');
});