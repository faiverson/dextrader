<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Repositories\UserRepository;
use App\Services\CommissionCreateValidator;
use App\Services\CommissionUpdateValidator;
use App\Repositories\CommissionRepository;
use Config;

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
		var_dump([
			'from_user_id' => $data['user_id'],
			'to_user_id' => $data['enroller_id'],
			'invoice_id' => $data['invoice_id'],
			'amount' => $data['amount'] * Config::get('dextrader.comms')
		]);
		if($data['enroller_id']) {
			$this->repository->create([
				'from_user_id' => $data['user_id'],
				'to_user_id' => $data['enroller_id'],
				'invoice_id' => $data['invoice_id'],
				'amount' => $data['amount'] * Config::get('dextrader.comms')
			]);
			$this->parent($data);
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
			Commission::create([
				'from_user_id' => $data['user_id'],
				'to_user_id' => $data['enroller_id'],
				'invoice_id' => $data['invoice_id'],
				'type' => 'parent',
				'amount' => $data['amount'] * Config::get('dextrader.parent_comms'),
			]);
		}
	}
}