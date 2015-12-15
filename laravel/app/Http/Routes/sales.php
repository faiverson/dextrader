<?php

Route::get('/secure/{url?}', function () {
    return view('sales');
})->where(['url' => '.*']);

Route::post('/sales', ['middleware' => 'page:checkout', 'uses' => 'PurchasesController@checkout']);

