<?php
namespace App\Gateways;

use App\Events\CommissionEvent;
use App\Gateways\AbstractGateway;
use App\Models\Commission;
use App\Repositories\UserRepository;
use App\Services\CommissionCreateValidator;
use App\Services\CommissionUpdateValidator;
use App\Repositories\CommissionRepository;
use Config;
use Event;
use Illuminate\Database\Eloquent\Collection;

class CommissionGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(CommissionRepository $repository, CommissionCreateValidator $createValidator, CommissionUpdateValidator $updateValidator, UserRepository $user, CommissionTotalGateway $totalGateway)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
		$this->user = $user;
		$this->totalGateway = $totalGateway;
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
			$this->totalGateway->add([
				'user_id' => $data['enroller_id'],
				'pending' => $data['amount'] * $enroller->commissions
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
				'from_user_id' => $parent->user_id,
				'to_user_id' => $parent->enroller_id,
				'invoice_id' => $data['invoice_id'],
				'type' => 'parent',
				'amount' => $data['amount'] * $parent->parent_commissions
			]);
			$this->totalGateway->add([
				'user_id' => $parent->enroller_id,
				'pending' => $data['amount'] * $parent->parent_commissions
			]);
			Event::fire(new CommissionEvent($comm));
		}
	}

	public function getCommissionToPay()
	{
		return $this->repository->getCommissionToPay();
	}

	public function getPendingToReady()
	{
		return $this->repository->getPendingToReady();
	}

	public function getPendingHoldbacksToReady()
	{
		return $this->repository->getPendingHoldbacksToReady();
	}

	public function updateToReady(Commission $commission)
	{
		$response = $this->repository->updateToReady($commission->ids);
		if($response) {
			return $this->totalGateway->setToReady($commission);
		}
		return $response;
	}

	public function updateHoldbackToReady(Commission $commission)
	{
		$response = $this->repository->updateHoldbackToReady($commission->ids);
		if($response) {
			return $this->totalGateway->setHoldbackToReady($commission);
		}
		return $response;
	}

	public function getUserCommissions($id, $limit, $offset, $order_by, $where)
	{
		return $this->repository->getUserCommissions($id, $limit, $offset, $order_by, $where);
	}

	public function getTotalUserCommissions($id, $where)
	{
		return $this->repository->getTotalUserCommissions($id, $where);
	}
}