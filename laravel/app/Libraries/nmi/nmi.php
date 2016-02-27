<?php
namespace App\Libraries\NMI;

use anlutro\cURL\Laravel\cURL;
use Log;
use Config;

class NMI {

	const APPROVED = 1;
	const DECLINED = 2;
	const ERROR = 3;
	const URL = 'https://secure.networkmerchants.com/api/transact.php';

	protected $username;
	protected $password;


	// Initial Setting Functions
	public function __construct()
	{
		$this->username = Config::get('nmi.username');;
		$this->password = Config::get('nmi.password');
	}

	public function purchase(array $data, $type = 'sale')
	{
		$order = [
			'username' => $this->username,
			'password' => $this->password,

			'ccnumber' => $data['number'],
			'ccexp' => $data['card_exp_month'] . $data['card_exp_year'],
			'amount' => number_format($data['amount'], 2, ".", ""),
			'cvv' => array_key_exists('cvv', $data) ? $data['cvv'] : '',

			'ipaddress' => array_key_exists('ip_address', $data) ? $data['ip_address'] : '',
			'orderid' => $data['orderid'],
			'orderdescription' => $data['description'],
			'tax' => 0,
			'shipping' => 0,
			'ponumber' => '',

			'firstname' => $data['first_name'],
			'lastname' => $data['last_name'],
			'email' => $data['email'],

			'address1' => $data['billing_address'],
			'address2' => array_key_exists('billing_address2', $data) ? $data['billing_address2'] : '',
			'country' => $data['billing_country'],
			'state' => $data['billing_state'],
			'city' => $data['billing_city'],
			'zip' => $data['billing_zip'],
			'phone' => array_key_exists('billing_phone', $data) ? $data['billing_phone'] : '',
			'type' => $type
		];
		Log::notice('Transactions gateway', $order);
		$values = array_map("urlencode", array_values($order));
		$this->data = array_combine(array_keys($order) , $values);
		return $this->send();
	}

	public function refund(array $data, $type ='sale')
	{
		$order = [
			'username' => $this->username,
			'password' => $this->password,

			'ccnumber' => $data['number'],
			'ccexp' => $data['card_exp_month'] . $data['card_exp_year'],
			'amount' => number_format($data['amount'], 2, ".", ""),
			'cvv' => array_key_exists('cvv', $data) ? $data['cvv'] : '',

			'ipaddress' => array_key_exists('ip_address', $data) ? $data['ip_address'] : '',
			'orderid' => $data['orderid'],
			'orderdescription' => $data['description'],
			'tax' => 0,
			'shipping' => 0,
			'ponumber' => '',

			'firstname' => $data['first_name'],
			'lastname' => $data['last_name'],
			'email' => $data['email'],

			'address1' => $data['billing_address'],
			'address2' => array_key_exists('billing_address2', $data) ? $data['billing_address2'] : '',
			'country' => $data['billing_country'],
			'state' => $data['billing_state'],
			'city' => $data['billing_city'],
			'zip' => $data['billing_zip'],
			'phone' => array_key_exists('billing_phone', $data) ? $data['billing_phone'] : '',
			'type' => $type
		];
		$values = array_map("urlencode", array_values($order));
		$this->data = array_combine(array_keys($order) , $values);
		return $this->send();
	}

	protected function send()
	{
		$response = null;
		$curl = cURL::post(self::URL, $this->data);
		$this->response = $curl->body;
		Log::notice('Purchase gateway info: ' . $curl->body);
		parse_str($curl->body, $response);
		return $response;
	}
}