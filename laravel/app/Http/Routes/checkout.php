<?php
Route::post('/checkout', ['middleware' => 'page:checkout',
						  'uses' => 'PurchasesController@checkout']);

