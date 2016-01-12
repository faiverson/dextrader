<?php
// it returns all the products and if they have offers per funnel url
Route::get('/offers/{funnel_id}', ['middleware' => 'page:checkout',
						  'uses' => 'SpecialOffersController@show'])->where('funnel_id', '[0-9]+');