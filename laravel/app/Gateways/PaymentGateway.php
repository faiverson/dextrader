<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Models\Commission;
use App\Repositories\PaymentRepository;
use App\Services\PaymentCreateValidator;
use App\Services\PaymentUpdateValidator;
use DateTime;

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

	public function payCommission($commissions)
	{
		// let grab the last payment to update balances
		$lastPayment = $this->repository->findLastByUser($commissions->user_id);
		$last = $lastPayment ? $lastPayment->balance : 0;
		$now = new DateTime('now');
		return $this->repository->create([
			'user_id' => $commissions->user_id,
			'prev_balance' => $last,
			'amount' => $commissions->total,
			'balance' => $last + $commissions->total,
			'ledger_type' => 'commissions',
			'paid_dt' => $now->format('Y-m-d H:i:s'),
			'info' => json_encode([
				'comms' => $commissions->comms_ids,
				'holdbacks' => $commissions->holdbacks_ids,
				'total_comms' => $commissions->total_comms,
				'total_holdbacks' => $commissions->total_holdbacks
			]),
		]);
	}

}