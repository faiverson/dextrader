<?php

class Twilio
{
	protected $_acct_sid;
	protected $_auth_token;
	protected $_api_version;

	public function __construct($acct_sid, $auth_token)
	{
		$this->_acct_sid = $acct_sid;
		$this->_auth_token = $auth_token;
		$this->_api_version = "2010-04-01";
	}

	public function txtSendMessage($from, $to, $body)
	{
		/****
		 * This function will send a text message from # to # with body as message
		 ****/
		$url = "https://api.twilio.com/" . $this->_api_version . "/Accounts/" . $this->_acct_sid . "/Messages/";
		//echo($url . "\n");
		$post_data = array("From" => $from,
			"To"   => $to,
			"Body" => $body
		);

		$process = curl_init($url);
		curl_setopt($process, CURLOPT_USERPWD, $this->_acct_sid . ":" . $this->_auth_token);
		curl_setopt($process, CURLOPT_TIMEOUT, 30);
		curl_setopt($process, CURLOPT_POST, 1);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($process, CURLOPT_POSTFIELDS, http_build_query($post_data));
		curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
		$return = curl_exec($process);
		curl_close($process);
		return $return;
	}
}