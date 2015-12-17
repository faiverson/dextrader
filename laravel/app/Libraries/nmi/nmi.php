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

	public function purchase(Purchase $purchase, $number, $ccv, $type = 'sale')
	{
		$order = [
			'username' => $this->username,
			'password' => $this->password,

			'ccnumber' => $number,
			'ccexp' => $purchase->card_exp_month . $purchase->card_exp_year,
			'amount' => number_format($purchase->product_amount, 2, ".", ""),
			'cvv' => $ccv,

			'ipaddress' => $purchase->ip_address,
			'orderid' => $purchase->id,
			'orderdescription' => $purchase->product_name,
			'tax' => 0,
			'shipping' => 0,
			'ponumber' => '',

			'firstname' => $purchase->first_name,
			'lastname' => $purchase->last_name,
			'email' => $purchase->email,
			'address1' => $purchase->billing_address,
			'address2' => $purchase->billing_address2,
			'country' => $purchase->billing_country,
			'state' => $purchase->billing_state,
			'city' => $purchase->billing_city,
			'zip' => $purchase->billing_zip,
			'phone' => $purchase->billing_phone,
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