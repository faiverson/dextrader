<?php

	require 'curl.php';
	
	$tradetype = 'm5';
	$tradeproduct = 'IB';
	$date = '12:05';
	$expire = '22:05';
	$direction = 1;
	$symbol = 'GBPUSD';	
	$open_price = 0.10243;
	$target_price = 0.14243;
	$close_price = 1.34243;
	$ticket = 54;
	$winloss = 1;

	$tradeproduct = strtolower($tradeproduct);

	$url = 'https://api.dextrader.com/api/';
	$params = [
		'domain' => 'signals', 
		'password' => 'siGN4l_dexTr4d3r'
	];
	$curl = new Curl;
	$curl->setHeader('ORIGIN', 'http://162.219.24.11');
    $curl->post($url . 'pages', $params);
	$response = (array) $curl->response;
	if($response["success"]) {
		$token = $response["data"]->token;
		$params = [
			'trade_type' => $tradetype,
			'type_product' => $tradeproduct,
			'close_price' => $close_price,
			'winloss' => 1
		];
		$curl->setHeader('Authorization', 'Bearer ' . $token);
    	$curl->put($url . 'signals/' . $ticket, $params);
    	var_dump($curl->response);
	}
	else {
		$file = fopen("signals.log", "w");
		fwrite($file, $print_r($curl->response, true));
		fclose($myfile);
	}

?>
