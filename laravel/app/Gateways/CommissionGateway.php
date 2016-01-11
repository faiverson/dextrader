<?php
namespace App\Gateways;

use App\Events\CommissionEvent;
use App\Gateways\AbstractGateway;
use App\Repositories\UserRepository;
use App\Services\CommissionCreateValidator;
use App\Services\CommissionUpdateValidator;
use App\Repositories\CommissionRepository;
use Config;
use Event;


class CommissionGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(CommissionRepository $repository, CommissionCreateValidator $createValidator, CommissionUpdateValidator $updateValidator, UserRepository $user)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
		$this->user = $user;

	}

	public function add(array $data)
	{
		if(array_key_exists('enroller_id', $data)) {
			$enroller = $this->user->find($data['enroller_id']);
			$comm = $this->repository->create([
				'from_user_id' => $data['user_id'],
				'to_user_id' => $data['enroller_id'],
				'invoice_id' => $data['invoice_id'],
				'amount' => $data['amount'] * $enroller->commissions
			]);
			$this->parent($data);
			Event::fire(new CommissionEvent($comm));
		}
	}

	/**
	 *
	 * Check if there is a parent and apply a comms
	 * if that's the case
	 *
	 * @param array $data
	 *
	 */
	public function parent(array $data)
	{
		$parent = $this->user->find($data['enroller_id']);
		if($parent->enroller_id > 0) {
			$comm = $this->repository->create([
				'from_user_id' => $data['user_id'],
				'to_user_id' => $data['enroller_id'],
				'invoice_id' => $data['invoice_id'],
				'type' => 'parent',
				'amount' => $data['amount'] * $parent->parent_commissions,
			]);
			Event::fire(new CommissionEvent($comm));
		}
	}

	public function getCommissionToPay()
	{
		return $this->repository->getCommissionToPay();
	}
}