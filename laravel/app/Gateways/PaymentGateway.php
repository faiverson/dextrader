<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Models\Commission;
use App\Repositories\PaymentRepository;
use App\Services\PaymentCreateValidator;
use App\Services\PaymentUpdateValidator;

class PaymentGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(PaymentRepository $repository, PaymentCreateValidator $createValidator, PaymentUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function getBalance($user_id)
	{
		$payment = $this->repository->findLastByUser($user_id);
		if($payment) {
			return $payment->balance;
		}
		return 0;
	}

	public function payCommission(Commission $commission, $balance)
	{
		$this->create([
			'user_id' => $commission->user_id,
			'prev_balance' => $balance,
			'balance' => $balance + $commission->amount,
			'ledger_type' => 'commission',
			'info' => $commission->id,
		]);
	}

}