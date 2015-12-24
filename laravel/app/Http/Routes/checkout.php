<?php
Route::post('/checkout', ['middleware' => 'page:checkout',
						  'uses' => 'TransactionController@checkout']);

