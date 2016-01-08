<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\CardCreateValidator;
use App\Services\CardUpdateValidator;
use App\Repositories\CardRepository;
use Cards;

class CardGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(CardRepository $repository, CardCreateValidator $createValidator, CardUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function validate(array $data)
	{
		if(!$this->createValidator->with($data)->passes()) {
			$this->errors = $this->createValidator->errors();
			return false;
		}

		$type = null;
		if(array_key_exists('network', $data)) {
			$type = $data['network'];
		}

		$card = Cards::validCreditCard($data['number'], $type);
		if(!$card['valid']) {
			$this->errors = ['The credit card number is not a valid number'];
			return false;
		}

		if(!Cards::validCvc($data['cvv'], $card['type'])) {
			$this->errors = ['The CVV number is invalid'];
			return false;
		}

		if(!Cards::validDate('20' . $data['exp_year'], $data['exp_month'])) {
			$this->errors = ['The expiration date is invalid'];
			return false;
		}

		if($this->repository->isCard($data['number'], $data['user_id']) > 0) {
			$this->errors = ['This credit card is in the system! Please contact support immediately!'];
			return false;
		}

		return $card;
	}

	public function findUserCard($user_id, $card_id)
	{
		$response = $this->repository->findUserCard($user_id, $card_id);
		if(!$response) {
			$this->errors = ['The credit card does not belong to the user'];
			return false;
		}

		return $response;
	}

	public function getLast($number)
	{
		return substr($number, -4);
	}

	public function getFirst($number)
	{
		return substr($number, 0, 6);
	}

}