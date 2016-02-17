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
use DateTime;

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
		if(array_key_exists('enroller_id', $data) && $data['amount'] > 0) {
			$enroller = $this->user->find($data['enroller_id']);
			$comm = $this->repository->create([
				'from_user_id' => $data['user_id'],
				'to_user_id' => $data['enroller_id'],
				'invoice_id' => $data['invoice_id'],
				'amount' => $data['amount'] * $enroller->commissions
			]);

			$this->totalGateway->add([
				'user_id' => $data['enroller_id'],
				'pending' => $data['amount'] * $enroller->commissions,
				'holdback' => $comm->holdback
			]);

			$this->parent($data);
			Event::fire(new CommissionEvent($comm));
		}
	}

	public function refund(array $data)
	{
		if(array_key_exists('invoice_id', $data)) {
			$comms = $this->repository->findByInvoice($data['invoice_id']);
			$now = new DateTime('now');
			foreach($comms as $c) {
				$this->repository->update([
					'refund_dt' => $now->format('Y-m-d H:i:s'),
					'refund_by' => $data['admin_id'],
					'status' => 'refund',
					'type' => 'refund'
				], $c->id);

				$this->totalGateway->add([
					'user_id' => $c->to_user_id,
					'pending' => $c->amount * (-1),
					'holdback' => $c->holdback * (-1)
				]);
			}
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
		if($parent->enroller_id > 0 && $data['amount'] > 0) {
			$comm = $this->repository->create([
				'from_user_id' => $parent->user_id,
				'to_user_id' => $parent->enroller_id,
				'invoice_id' => $data['invoice_id'],
				'type' => 'parent',
				'amount' => $data['amount'] * $parent->parent_commissions
			]);

			$this->totalGateway->add([
				'user_id' => $parent->enroller_id,
				'pending' => $data['amount'] * $parent->parent_commissions,
				'holdback' => $comm->holdback
			]);

			Event::fire(new CommissionEvent($comm));
		}
	}

	public function getCommissionToPay(DateTime $date)
	{
		return $this->repository->getCommissionToPay($date);
	}

	public function getHoldbackToPay()
	{
		return $this->repository->getHoldbackToPay();
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
		if($commission->comms_ids) {
			$response = $this->repository->updateToReady($commission->comms_ids);
			if(!$response) {
				$this->errors = ['Failed updateToReady ' . $commission->comms_ids];
				return false;
			}
		}

		if($commission->holdbacks_ids) {
			$response = $this->repository->updateHoldbackToReady($commission->ids);
			if(!$response) {
				$this->errors = ['Failed updateHoldbackToReady ' . $commission->comms_ids];
				return false;
			}
		}

		if($response) {
			$response = $this->totalGateway->setToReady($commission);
			if($response) {
				$this->errors = ['Failed totalGateway setToReady '];
				return false;
			}
		}
		return $response;
	}

	public function payCommissionOnNextDate(Commission $commission)
	{
		if($commission->comms_ids) {
			$response = $this->repository->payCommissionOnNextDate($commission->comms_ids);
			if (!$response) {
				$this->errors = ['Failed payCommissionOnNextDate ' . $commission->comms_ids];
				return false;
			}
		}

		if($commission->holdbacks_ids) {
			$response = $this->repository->payHoldbacksOnNextDate($commission->holdbacks_ids);
			if(!$response) {
				$this->errors = ['Failed payHoldbacksOnNextDate ' . $commission->holdbacks_ids];
				return false;
			}
		}

		return $response;
	}

	public function updateToPaid(Commission $commission)
	{
		if($commission->comms_ids) {
			$c_response = $this->repository->updateToPaid($commission->comms_ids);
			if(!$c_response) {
				$this->errors = ['Failed updateToPaid ' . $commission->comms_ids];
				return false;
			}
		}

		if($commission->holdbacks_ids) {
			$h_response = $this->repository->updateHoldbackToPaid($commission->holdbacks_ids);
			if(!$h_response) {
				$this->errors = ['Failed updateHoldbackToPaid ' . $commission->holdbacks_ids];
				return false;
			}
		}

		$response = $this->totalGateway->setToPaid($commission);
		if(!$response) {
			$this->errors = ['Failed setToPaid '];
			return false;
		}
		return true;
	}

	public function getUserCommissions($id, $limit, $offset, $order_by, $where)
	{
		return $this->repository->getUserCommissions($id, $limit, $offset, $order_by, $where);
	}

	public function getTotalUserCommissions($id, $where)
	{
		return $this->repository->getTotalUserCommissions($id, $where);
	}

	public function getSummaryUserCommissions($id)
	{
		return $this->repository->getSummaryUserCommissions($id);
	}

	public function getBalance($id)
	{
		return $this->totalGateway->findBy('user_id', $id);
	}


}