<?php

//Route::get('/secure/{url?}', function () {
//    return view('sales');
//})->where(['url' => '.*']);

Route::post('/checkout', ['middleware' => 'page:checkout',
						  'uses' => 'PurchasesController@checkout']);

