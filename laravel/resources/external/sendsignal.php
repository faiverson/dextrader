<?php
	require 'curl.php';
	$dbname = $_POST['dbname'];
	$dbpass = $_POST['dbpass'];
	$period = $_POST['period'];
	$tradetype = $_POST['tradetype'];
	$tradeproduct = strtolower($_POST['tradeproduct']);

	$cmd = $_POST['cmd'];
	$date = $_POST['date'];
	$expire = $_POST['expire'];
	$direction = $_POST['direction'];
	$symbol = $_POST['symbol'];	
	$open_price = $_POST['open_price'];
	$target_price = $_POST['target_price'];
	$close_price = $_POST['close_price'];
	$ticket = $_POST['ticket'];
	$winloss = 0;

	$myfile = fopen("signal.txt", "w");
	fwrite($myfile, $print_r($_POST, true));
	fclose($myfile);
	$dbuser = $dbname;
	
	/*if ( $dbuser == "" ) 
	{
		$dbname = "maxxwell_signal";
		$dbuser = "maxxwell_signal";
		$dbpass = "december69";
		$tradetype = "nadex";
		$tradeproduct = "1h";

		$cmd = "close";
		$date = 'adf';
		$expire = 'asdf';
		$direction = 1;
		$symbol = 'asdfsd';
		$open_price = 1.231;
		$target_price = 1.432;
		$close_price = 1.342;
		$ticket = 1;
	}*/

	$tblname = strtolower($tradetype) . "_" . $tradeproduct;

	$link = mysqli_connect("localhost", $dbuser, $dbpass, $dbname);

	if (!$link || $tblname == "_") {
		echo '0';
		die();
	}

	if (true)
	{
		$query = sprintf("SELECT `%s`;", $tblname);
		$result = mysqli_query($link, $query);

		if ( !$result )
		{
			$sql = "CREATE TABLE IF NOT EXISTS `%s` (" . 
						"  `id` int(11) NOT NULL AUTO_INCREMENT," . 
						"  `date` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''," . 
						"  `expire` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''," . 
						"  `direction` int(11) NOT NULL DEFAULT '0'," . 
						"  `symbol` varchar(255) COLLATE utf8_unicode_ci NOT NULL," . 
						"  `open_price` float NOT NULL DEFAULT '0'," . 
						"  `target_price` float NOT NULL DEFAULT '0'," . 
						"  `close_price` float NOT NULL DEFAULT '0'," . 
						"  `winloss` int(11) NOT NULL DEFAULT '0'," . 
						"  `updated` int(11) NOT NULL DEFAULT '1'," . 
						"  PRIMARY KEY (`id`)" . 
						") ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;";

			$query = sprintf( $sql, $tblname );

			$result = mysqli_query($link, $query);

			if ( !$result ) {
				echo '0';
				mysqli_close($link);
				die();
			}
		}
	}

	if ( $cmd == 'open' )
	{
		// INSERT INTO tbl_signals (`date`, `updated', 'winloss', 'symbol' ) VALUES ('$date', '$updated, 
		$query = sprintf("INSERT INTO `%s` VALUES (NULL, '%s', '%s', '%s', '%s', '%s', '%s', '0', '0', '1');", $tblname, $date, $expire, $direction, $symbol, $open_price, $target_price );
		$result = mysqli_query($link, $query);

		if ( !$result ) {
			echo '0';
			mysqli_close($link);
			die();
		}

		//$row_id = mysqli_insert_id($link);
		//echo $row_id;

		$query = sprintf("SELECT * FROM `%s` ORDER BY id DESC LIMIT 1;", $tblname);
		$result = mysqli_query($link, $query);
		$ticket = mysqli_insert_id();
		if ( !$result ) {
			echo '0';
			mysqli_close($link);
			die();
		}
		mysqli_free_result($result);

		// EVERYTHING GETS SENT TO DEXTRADER VIA API HERE
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
				'signal_time' => $date,
				'expiry_time' => $expire,
				'asset' => $symbol,
				'open_price' => $open_price,
				'target_price' => $target_price,
				'type_product' => $tradeproduct,
				'mt_id' => $ticket,
				'trade_type' => $tradetype,
				'winloss' => $winloss
			];
			$curl->setHeader('Authorization', 'Bearer ' . $token);
	    	$curl->post($url . 'signals', $params);
		}
		else {
		$file = fopen("signals-error.log", "w");
		fwrite($file, $print_r($curl->response, true));
		fclose($myfile);
	}
	}
	else if ( $cmd == 'close' )
	{
		$query = sprintf("SELECT * FROM `%s` WHERE id='%d';", $tblname, $ticket );
		$result = mysqli_query($link, $query);

		if ( !$result ) {
			echo '0';
			mysqli_close($link);
			die();
		}

		$row = mysqli_fetch_assoc($result);

		if ( $row['direction'] == "0" )
		{
			if ( floatval($row['open_price']) <= floatval($close_price) )
				$query = sprintf("UPDATE `%s` SET close_price='%s', winloss=1 WHERE id='%d';", $tblname, $close_price, $ticket );
				$winloss = 1;
			else
				$query = sprintf("UPDATE `%s` SET close_price='%s', winloss=0 WHERE id='%d';", $tblname, $close_price, $ticket );
		}
		else if ( $row['direction'] == "1" )
		{
			if ( floatval($row['open_price']) >= floatval($close_price) )
				$query = sprintf("UPDATE `%s` SET close_price='%s', winloss=1 WHERE id='%d';", $tblname, $close_price, $ticket );
				$winloss = 1;
			else
				$query = sprintf("UPDATE `%s` SET close_price='%s', winloss=0 WHERE id='%d';", $tblname, $close_price, $ticket );
		}

		
		$result = mysqli_query($link, $query);

		if ( !$result ) {
			echo '0';
			mysqli_close($link);
			die();
		}

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
				'winloss' => $winloss
			];
			$curl->setHeader('Authorization', 'Bearer ' . $token);
	    	$curl->put($url . 'signals/' . $ticket, $params);
	    	var_dump($curl->response);
		}
		else {
			$file = fopen("signals-error.log", "w");
			fwrite($file, $print_r($curl->response, true));
			fclose($myfile);
		}

		echo '1';
	}

	mysqli_close($link);

?>
