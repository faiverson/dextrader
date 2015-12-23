<?php
namespace App\Gateways;

use App\Repositories\TransactionRepository;
use App\Services\TransactionCreateValidator;
use App\Services\TransactionUpdateValidator;
use App\Gateways\AbstractGateway;
use App\Gateways\UserGateway;
use App\Gateways\ProductGateway;
use App\Gateways\CardGateway;
use App\Gateways\TagGateway;
use App\Libraries\nmi\nmi;

class TransactionGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $user;

	protected $card;

	protected $cvv;

	protected $tag;

	protected $errors;

	public function __construct(TransactionRepository $repository,
								TransactionCreateValidator $transactionCreateValidator,
								TransactionUpdateValidator $transactionUpdateValidator,
								UserGateway $user,
								ProductGateway $product,
								CardGateway $card,
								TagGateway $tag)
	{
		$this->repository = $repository;
		$this->createValidator = $transactionCreateValidator;
		$this->updateValidator = $transactionUpdateValidator;
		$this->user = $user;
		$this->product = $product;
		$this->card = $card;
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

		// get info about user and product
		$user = $this->user->find($data['user_id']);
		$product = $this->product->find($data['product_id']);
		if(array_key_exists('tag', $data)) {
			$data['tag_id'] = $this->tag->getIdByTag($data['tag']);
		}

		if(array_key_exists('enroller', $data)) {
			$data['enroller_id'] = $this->user->getIdByUsername($data['enroller']);
		}

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
			$this->errors = ['Something wrong when the transaction was created!'];
			return false;
		}
		$data['orderid'] = $transaction['id'];
		$gateway = $this->gateway($data);
		$response = $this->set($gateway, $transaction['id']);
		if(!$response) {
			$this->errors = ['Something wrong when the transaction was updated!'];
			return false;
		}
		return $gateway;
	}

	public function gateway($data)
	{
		$nmi = new NMI;
		return $nmi->purchase($data);
	}

	public function set($data, $id)
	{
		return $this->repository->update($data, $id);
	}
}