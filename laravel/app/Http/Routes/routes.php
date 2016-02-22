<?php

Route::group(['namespace' => 'Auth', 'prefix' => 'api'], function () {
	Route::post('/login', 'AuthController@login');
	Route::post('/pages', 'AuthController@pages');
	Route::get('/logout', 'AuthController@logout');
	Route::post('/password', 'PasswordController@postEmail');
	Route::post('/password/reset', 'PasswordController@postReset');
});

//Route::get('/testing', function() {
//	$a = '{"response":"1","responsetext":"SUCCESS","authcode":"123456","transactionid":"2999806912","avsresponse":"N","cvvresponse":"N","orderid":70,"type":"sale","response_code":"100","products":[{"product_id":1,"product_name":"IB","product_display_name":"IB","product_amount":"47.00","product_discount":"0.00","billing_period":"+1 month","roles":"IB"}],"tag":"nirvana","user_id":"60","funnel_id":"1","billing_address":"Rogelio Martinez 334","billing_city":"coroda","billing_state":"cordoba capital","billing_country":"argentina","billing_zip":"15606","billing_phone":"34234234234234","card_name":"Gregory Peers","card_exp_month":"11","card_exp_year":"17","cvv":"314","number":"5394835291271361","enroller":"hegmann_jermain","ip_address":"181.110.13.76","enroller_id":48,"tag_id":5,"info":{"ip":"127.0.0.1","isoCode":"US","country":"United States","city":"New Haven","state":"CT","postal_code":"06510","lat":41.31,"lon":-72.92,"timezone":"America\/New_York","continent":"NA","default":true},"first_name":"Mortimer","last_name":"Stroman","email":"fabian@fem-inc.com","username":"corkery_amber","card_network":"mastercard","description":"IB","card_last_four":"1361","card_first_six":"539483","amount":47,"card_id":12,"billing_address_id":12,"transaction_id":70,"invoice_id":33}';
//	Event::fire(new \App\Events\CheckoutEvent(json_decode($a, true)));
////	Event::fire(new \App\Events\RefundEvent([
////		'invoice_id' => 4,
////		'admin_id' => 4
////	]));
//});

//Route::get('/testing', function() {
//	$redis = \Illuminate\Support\Facades\Redis::connection();
//	$redis->set("fafa", "jojo!");
//	return $redis->get("fabi");
//});

//Event::listen('illuminate.query', function($query, $params)
//{
//	print $query.'<br>';
//	var_dump($params);
//	exit;
//});

//Event::listen('router.matched', function($route, $request)
//{
//	print $query.'<br>';
//	var_dump($request->path());
//});