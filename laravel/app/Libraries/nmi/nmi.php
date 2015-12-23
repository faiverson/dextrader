<?php
namespace App\Libraries\NMI;

use anlutro\cURL\Laravel\cURL;
use Log;
use Config;
use App\Models\Purchase;

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
			'cvv' => $data['cvv'],

			'ipaddress' => $data['ip_address'],
			'orderid' => $data['orderid'],
			'orderdescription' => $data['product_name'],
			'tax' => 0,
			'shipping' => 0,
			'ponumber' => '',

			'firstname' => $data['first_name'],
			'lastname' => $data['last_name'],
			'email' => $data['email'],

			'address1' => $data['billing_address'],
			'address2' => $data['billing_address2'],
			'country' => $data['billing_country'],
			'state' => $data['billing_state'],
			'city' => $data['billing_city'],
			'zip' => $data['billing_zip'],
			'phone' => $data['billing_phone'],
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
		Log::info('Purchase ' . $curl->body);
		parse_str($curl->body, $response);
		return $response;
	}
}