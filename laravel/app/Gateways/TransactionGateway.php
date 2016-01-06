<?php
namespace App\Gateways;

use App\Models\Subscription;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Services\TransactionCreateValidator;
use App\Services\TransactionUpdateValidator;
use App\Gateways\AbstractGateway;
use App\Gateways\TransactionDetailGateway;
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
use Illuminate\Database\Eloquent\Collection;
use Event;
use App\Events\SubscriptionRenewedEvent;

class TransactionGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $detail;

	protected $purchase;

	protected $subscription;

	protected $invoice;

	protected $user;

	protected $product;

	protected $card;

	protected $address;

	protected $tag;

	protected $errors;

	public function __construct(TransactionRepository $repository,
								TransactionCreateValidator $transactionCreateValidator,
								TransactionUpdateValidator $transactionUpdateValidator,
								TransactionDetailGateway $detail,
								PurchaseGateway $purchase,
								SubscriptionGateway $subscription,
								InvoiceGateway $invoice,
								UserGateway $user,
								ProductGateway $product,
								CardGateway $card,
								BillingAddressGateway $address,
								TagGateway $tag)
	{
		$this->repository = $repository;
		$this->detail = $detail;
		$this->createValidator = $transactionCreateValidator;
		$this->updateValidator = $transactionUpdateValidator;
		$this->purchase = $purchase;
		$this->subscription = $subscription;
		$this->invoice = $invoice;
		$this->user = $user;
		$this->product = $product;
		$this->card = $card;
		$this->address = $address;
		$this->tag = $tag;

	}

	public function findBy($attribute, $value, $columns = array('*'), $limit = null, $offset = null, $order_by = null) {
		return $this->repository->findBy($attribute, $value, $columns, $limit, $offset, $order_by);
	}

	public function findWith($id) {
		return $this->repository->findWith($id);
	}

	/**
	 * @param array $data
	 * @return object Transaction
	 *
	 */
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
		$products = $this->product->findIn($data['products']);
		$subs = $this->subscription->findBy('user_id', $data['user_id'], ['product_id']);
		$canBuy = $this->product->userCanBuy($subs, $products);
		if(!$canBuy) {
			$this->errors = $this->product->errors();
			return false;
		}

		if(array_key_exists('tag', $data)) {
			$tag = $this->tag->getIdByTag($data['user_id'], $data['tag']);
			if($tag) {
				$data['tag_id'] = $tag->id;
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
		$amount = $this->product->total($products);
		$data = array_merge($data, [
			'first_name' => $user->first_name,
			'last_name' => $user->last_name,
			'email' => $user->email,
			'card_network' => $card['type'],
			'description' => implode(array_column($products->toArray(), 'name'), ' - '),
			'products' => $this->setDetail($products),
			'card_last_four' => $this->card->getLast($data['number']),
			'card_first_six' => $this->card->getFirst($data['number']),
			'amount' => $amount
		]);

		$transaction = $this->create($data);
		if(!$transaction) {
			$this->errors = $this->errors();
			return false;
		}

		// connect to the gateway merchant
		$data['orderid'] = $transaction->id;
		$gateway = $this->gateway($data);

		// save the response in the transaction
		$response = $this->set($gateway, $transaction->id);
		if(!$response) {
			$this->errors = $this->errors();
			return false;
		} else {
			$data = array_merge($gateway, $data);
		}

		return $data;
	}

	public function setDetail(Collection $products)
	{
		$p = [];
		foreach($products as $product) {
			array_push($p, [
				'product_id' => $product->id,
				'product_name' => $product->name,
				'product_display_name' => $product->display_name,
				'product_amount' => $product->amount,
				'product_discount' => $product->discount,
				'billing_period' => $product->billing_period,
				'roles' => $product->roles,
			]);
		}
		return $p;
	}

	public function create(array $data)
	{
		DB::beginTransaction();
		try {
			if( ! $this->createValidator->with($data)->passes() )
			{
				$this->errors = $this->createValidator->errors();
				return false;
			}

			$transaction = $this->repository->create($data);
			if($transaction) {
				foreach($data['products'] as $product) {
					$product['transaction_id'] = $transaction->id;
					$detail = $this->detail->create($product);
					if(!$detail) {
						$this->errors = $this->detail->errors();
						return false;
					}
				}
			}
		} catch(\Exception $e) {
			DB::rollback();
			$this->errors = [$e->getMessage()];
			return false;
		}
		DB::commit();
		return $transaction;
	}

	public function gateway(array $data)
	{
		$nmi = new NMI;
		return $nmi->purchase($data);
	}

	public function set(array $data, $id)
	{
		return $this->update($data, $id);
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
				'address2' => array_key_exists('billing_address2', $data) ? $data['billing_address2'] : '',
				'country' => $data['billing_country'],
				'state' => $data['billing_state'],
				'city' => $data['billing_city'],
				'zip' => $data['billing_zip'],
				'phone' => array_key_exists('billing_phone', $data) ? $data['billing_phone'] : '',
			]);
			if(!$billing) {
				$this->errors = $this->address->errors();
				return false;
			}

			$invoice = $this->invoice->create(array_merge($data, [
				'card_id' => $card->id,
				'billing_address_id' => $billing->id,
				'status' => 'active',
				'transaction_id' => $data['orderid']
			]));
			if(!$invoice) {
				$this->errors = $this->invoice->errors();
				return false;
			}

			$now = new DateTime('now');
			foreach($data['products'] as $product) {
				$next = new DateTime('now');
				$next->modify($product['billing_period']);
				$subscription = $this->subscription->create(array_merge($data, [
					'card_id' => $card->id,
					'billing_address_id' => $billing->id,
					'status' => 'active',
					'product_id' => $product['product_id'],
					'last_billing' => $now->format('Y-m-d'),
					'next_billing' => $next->format('Y-m-d'),
					// if there multiple products, we don't want the total
					// we want to get the product's price since the sub is related to
					// one product only
					'amount' => number_format($product['product_amount'] - ($product['product_amount'] * $product['product_discount']), 2, '.', ',')
				]));
				if (!$subscription) {
					$this->errors = $this->subscription->errors();
					return false;
				}

				$invoice_detail = $this->invoice->addDetail(array_merge($product, [
					'subscription_id' => $subscription->id,
					'invoice_id' => $invoice->id
				]));
				if (!$invoice_detail) {
					$this->errors = $this->invoice->errors();
					return false;
				}

				$purchase = $this->purchase->create(array_merge($data, [
					'invoice_id' => $invoice->id,
					'card_id' => $card->id,
					'billing_address_id' => $billing->id,
					'subscription_id' => $subscription->id,
					'transaction_id' => $data['orderid']
				]));
				if(!$purchase) {
					$this->errors = $this->purchase->errors();
					return false;
				}

				$role_id = $this->user->getRoleByName($product['roles']);
				$this->user->attachRole($data['user_id'], $role_id);
			}
		}
		catch(\Exception $e) {
			DB::rollback();
			$this->errors = [$e->getMessage()];
			return false;
		}
		DB::commit();

		return array_merge($data, [
			'card_id' => $card->id,
			'billing_address_id' => $billing->id,
			'transaction_id' => $data['orderid'],
			'invoice_id' => $invoice->id,
		]);
	}

	/**
	 * Generate invoice when we have a new transaction to renew a subscription
	 *
	 * @param $data
	 * @return bool
	 */
	public function generateInvoice($data)
	{
		DB::beginTransaction();
		try {
			$invoice = $this->invoice->create($data);
			if(!$invoice) {
				$this->errors = $this->invoice->errors();
				return false;
			}

			foreach($data['products'] as $product) {
				$invoice_detail = $this->invoice->addDetail(array_merge($product, [
					'invoice_id' => $invoice->id,
					'subscription_id' => $data['subscription_id'],
				]));
				if (!$invoice_detail) {
					$this->errors = $this->invoice->errors();
					return false;
				}
				Event::fire(new SubscriptionRenewedEvent([
					'subscription_id' => $data['subscription_id'],
					'user_id' => $data['user_id'],
					'enroller_id' => $data['enroller_id'],
					'invoice_id' => $invoice->id,
					'amount' => $data['amount']
				]));
			}
		}
		catch(\Exception $e) {
			DB::rollback();
			$this->errors = [$e->getMessage()];
			return false;
		}
		DB::commit();

		return $invoice;
	}

	protected function setInfo(array $info)
	{
		$geo = GeoIP::getLocation($info['ip_address']);
		if(array_key_exists('info', $info)) {
			return array_merge($geo, $info['info']);
		}
		return $geo;
	}

	/**
	 * @param array $data
	 * @return object Transaction
	 *
	 */
	public function upgrade(array $data)
	{
		// we check if the card data is valid
		$card = $this->card->findUserCard($data['user_id'], $data['card_id']);
		$billing_address = $this->address->findByUser($data['user_id'], $data['billing_address_id']);

		// get info about user, product and tags
		$user = $this->user->find($data['user_id']);
		$products = $this->product->findIn($data['products']);
		$subs = $this->subscription->findBy('user_id', $data['user_id'], ['product_id']);
		$canBuy = $this->product->userCanBuy($subs, $products);
		if(!$canBuy) {
			$this->errors = $this->product->errors();
			return false;
		}

		// add geo location
		$data['info'] = $this->setInfo($data);

		// prepare the information
		$amount = $this->product->total($products);
		$data = array_merge($data, [
			'first_name' => $user->first_name,
			'last_name' => $user->last_name,
			'email' => $user->email,
			'card_network' => $card['type'],
			'description' => implode(array_column($products->toArray(), 'name'), ' - '),
			'products' => $this->setDetail($products),
			'card_last_four' => $this->card->getLast($data['number']),
			'card_first_six' => $this->card->getFirst($data['number']),
			'amount' => $amount
		]);

		$transaction = $this->create($data);
		if(!$transaction) {
			$this->errors = $this->errors();
			return false;
		}

		// connect to the gateway merchant
		$data['orderid'] = $transaction->id;
		$gateway = $this->gateway($data);

		// save the response in the transaction
		$response = $this->set($gateway, $transaction->id);
		if(!$response) {
			$this->errors = $this->errors();
			return false;
		} else {
			$data = array_merge($gateway, $data);
		}

		return $data;
	}


}