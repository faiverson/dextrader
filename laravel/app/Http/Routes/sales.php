<?php
Route::post('/sales', ['middleware' => 'page:checkout', 'uses' => 'PurchasesController@checkout']);

