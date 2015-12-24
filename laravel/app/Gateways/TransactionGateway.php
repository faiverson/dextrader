<?php
namespace App\Gateways;

use App\Repositories\TransactionRepository;
use App\Services\TransactionCreateValidator;
use App\Services\TransactionUpdateValidator;
use App\Gateways\AbstractGateway;
use App\Gateways\PurchaseGateway;
use App\Gateways\SubscriptionGateway;
use App\Gateways\InvoiceGateway;
use App\Gateways\UserGateway;
use App\Gateways\RoleGateway;
use App\Gateways\ProductGateway;
use App\Gateways\CardGateway;
use App\Gateways\TagGateway;
use App\Libraries\nmi\nmi;
use GeoIP;
use DB;
use DateTime;

class TransactionGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $purchase;

	protected $user;

	protected $card;

	protected $address;

	protected $cvv;

	protected $tag;

	protected $errors;

	public function __construct(TransactionRepository $repository,
								TransactionCreateValidator $transactionCreateValidator,
								TransactionUpdateValidator $transactionUpdateValidator,
								PurchaseGateway $purchase,
								SubscriptionGateway $subscription,
								InvoiceGateway $invoice,
								UserGateway $user,
								RoleGateway $role,
								ProductGateway $product,
								CardGateway $card,
								BillingAddressGateway $address,
								TagGateway $tag)
	{
		$this->repository = $repository;
		$this->createValidator = $transactionCreateValidator;
		$this->updateValidator = $transactionUpdateValidator;
		$this->purchase = $purchase;
		$this->subscription = $subscription;
		$this->invoice = $invoice;
		$this->user = $user;
		$this->role = $role;
		$this->product = $product;
		$this->card = $card;
		$this->address = $address;
		$this->tag = $tag;

	}

	public function add(array $data)
	{
		// we check if the card data is valid
		$this->cvv = $data['cvv'];
		$card = $this->card->validate([
			'user_id' => $data['user_id'],
			'name' => $data['card_name'],
			'number' => $data['number'],
			'exp_month' => $data['card_exp_month'],
			'exp_year' => $data['card_exp_year'],
			'cvv' => $this->cvv,
		]);

		if(!$card) {
			$this->errors = $this->card->errors();
			return false;
		}

		// get info about user, product and tags
		$user = $this->user->find($data['user_id']);
		$product = $this->product->find($data['product_id']);

		if(array_key_exists('tag', $data)) {
			$tag_id = $this->tag->getIdByTag($data['tag']);
			if($tag_id) {
				$data['tag_id'] = $tag_id;
			}
		}

		if(array_key_exists('enroller', $data)) {
			$enroller_id = $this->user->getIdByUsername($data['enroller']);
			if($enroller_id) {
				$data['enroller_id'] = $enroller_id;
			}
		}

		// add geo location
		$data['info'] = $this->setInfo($data);

		// prepare the information
		$data = array_merge([
			'first_name' => $user->first_name,
			'last_name' => $user->last_name,
			'email' => $user->email,
			'card_network' => $card['type'],
			'product_name' => $product->name,
			'product_amount' => $product->amount,
			'product_discount' => $product->discount,
			'card_last_four' => $this->card->getLast($data['number']),
			'card_first_six' => $this->card->getFirst($data['number']),
			'amount' => $this->product->price($product)
		], $data);

		$transaction = $this->create($data);
		if(!$transaction) {
			$this->errors = $this->errors();
			return false;
		}

		// connect to the gateway merchant
		$data['orderid'] = $transaction['id'];
		$data['period'] = $product->period;

//		$gateway = ['responsetext' => 'success']; // emulate success gateway
		$gateway = $this->gateway($data);

		// save the response in the transaction
		$response = $this->set($gateway, $transaction['id']);
		if(!$response) {
			$this->errors = $this->errors();
			return false;
		}

		// return all the info
		return array_merge($data, $gateway);
	}

	public function gateway(array $data)
	{
		$nmi = new NMI;
		return $nmi->purchase($data);
	}

	public function set(array $data, $id)
	{
		return $this->repository->update($data, $id);
	}

	public function purchase(array $data)
	{
		DB::beginTransaction();
		try {
			$card = $this->card->create([
				'user_id' => $data['user_id'],
				'number' => $data['number'],
				'network' => $data['card_network'],
				'name' => $data['card_name'],
				'exp_month' => $data['card_exp_month'],
				'exp_year' => $data['card_exp_year'],
				'last_four' => $data['card_last_four'],
				'first_six' => $data['card_first_six'],
			]);
			if(!$card) {
				$this->errors = $this->card->errors();
				return false;
			}

			$billing = $this->address->create([
				'user_id' => $data['user_id'],
				'address' => $data['billing_address'],
				'address2' => $data['billing_address2'],
				'country' => $data['billing_country'],
				'state' => $data['billing_state'],
				'city' => $data['billing_city'],
				'zip' => $data['billing_zip'],
				'phone' => $data['billing_phone'],
			]);

			if(!$billing) {
				$this->errors = $this->address->errors();
				return false;
			}

			$now = new DateTime('now');
			$next = new DateTime('now');
			$next->modify($data['period']);
			$subscription = $this->subscription->create(array_merge($data, [
				'card_id' => $card->id,
				'billing_address_id' => $billing->id,
				'status' => 'active',
				'last_billing' => $now->format('Y-m-d'),
				'next_billing' => $next->format('Y-m-d')
			]));
			if(!$subscription) {
				$this->errors = $this->subscription->errors();
				return false;
			}

			$invoice = $this->invoice->create(array_merge($data, [
				'card_id' => $card->id,
				'billing_address_id' => $billing->id,
				'status' => 'active',
				'subscription_id' => $subscription->id,
				'transaction_id' => $data['orderid']
			]));
			if(!$invoice) {
				$this->errors = $this->invoice->errors();
				return false;
			}

			$role_id = $this->role->getRoleIdByName($data['product_name']);
			$this->user->attachRole($data['user_id'], $role_id);
		} catch(\Exception $e) {
			DB::rollback();
			$this->errors = [$e->getMessage()];
			return false;
		}
		DB::commit();

		return array_merge($data, [
			'card_id' => $card->id,
			'billing_address_id' => $billing->id,
			'status' => 'active',
			'subscription_id' => $subscription->id,
			'transaction_id' => $data['orderid'],
			'invoice_id' => $invoice->id
		]);
	}

	protected function setInfo(array $info)
	{
		$geo = GeoIP::getLocation($info['ip_address']);
		if(array_key_exists('info', $info)) {
			return array_merge($geo, $info['info']);
		}
		return $geo;
	}
}