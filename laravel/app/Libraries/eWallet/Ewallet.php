<?php

namespace App\Libraries\eWallet;

use anlutro\cURL\Laravel\cURL;
use User;
use Settings;
use Log;
/*
This class deals with eWallet iPayout systems
*/
class eWallet
{
	protected $merchantName;
	protected $merchantGUID;
	protected $merchantPassword;
	protected $eWalletAPIURL;
	protected $user;
	protected $curl;
	protected $userId;

	public function __construct($user)
	{
		$this->merchantName = 'Dex Trader';
		$this->merchantGUID = 'ddd85e30-a29e-4baf-a84c-0ac4116e3405';
		$this->eWalletURL = "https://testewallet.com/eWalletWS/ws_Adapter.aspx";
		$this->merchantPassword = 'umwves16Jg';
		$this->user = $user;
		$this->userId = $user->id;
	}

	public function create()
	{
		$user = $this->user->toArray();
		$info = [
			'fn'    			=> 'eWallet_RegisterUser',
			'MerchantGUID'		=> $this->merchantGUID,
			'MerchantPassword'	=> $this->merchantPassword,
			'UserName'			=> $user['username'],
			'FirstName'			=> $user['first_name'],
			'LastName'			=> $user['last_name'],
//			'CompanyName'		=> '',
//			'Address1'			=> '',
//			'Address2'			=> '',
//			'City'				=> '',
//			'State'				=> '',
//			'ZipCode'			=> '',
//			'Country2xFormat'	=> '',
//			'PhoneNumber'		=> $user['phone'],
//			'CellPhoneNumber'	=> '',
			'EmailAddress'		=> $user['email'],
//			'CompanyTaxID'		=> '',
//			'GovernmentID'		=> '',
//			'MilitaryID'		=> '',
//			'PassportNumber'	=> '',
//			'DriversLicense'	=> '',
			'DateOfBirth'		=> '01/01/1900',
			'DefaultCurrency'	=> 'USD'
		];

		$curl = cURL::post($this->eWalletURL, $info);
		$response = $this->parseResponse($curl->body);
		Log::info('eWallet user: '. $this->userId, $response);
		if ($response['code'] == 'USERNAME_EXISTS') {
			// we override the response because we want to autologin the user
			$response = $this->login();
		}

		return $response;
	}

	public function login()
	{
		$user = $this->user->toArray();

		$info = array(
			"fn"               => "eWallet_RequestUserAutoLogin",
			'MerchantGUID'		=> $this->merchantGUID,
			'MerchantPassword'	=> $this->merchantPassword,
			'UserName'			=> $user['username'],
		);

		$curl = cURL::post($this->eWalletURL, $info);
//		Log::info('eWallet user check: '. $this->userId, $curl->body);
		$response = $this->parseResponse($curl->body);

		return $response;
	}

	protected function parseResponse($response)
	{
		parse_str(urldecode($response), $body);
		parse_str($body['response'], $code);
		return array(
			'message' => $body['m_Text'],
			'code' => $code['m_Code']
		);
	}
}
