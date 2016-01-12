<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Models\Commission;
use App\Services\CommissionTotalCreateValidator;
use App\Services\CommissionTotalUpdateValidator;
use App\Repositories\CommissionTotalRepository;
use Illuminate\Database\Eloquent\Collection;

class CommissionTotalGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(CommissionTotalRepository $repository, CommissionTotalCreateValidator $createValidator, CommissionTotalUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function add(array $data)
	{
		$user_id = $data['user_id'];
		$commsTotal = $this->repository->findByUserId($user_id);
		if($commsTotal) {
			$commsTotal->pending += $data['pending'];
			$commsTotal->save();
		}
		else {
			$commsTotal = $this->repository->create($data);
		}
		return $commsTotal;
	}

	public function setToReady(Commission $commission)
	{
		$user_id = $commission->user_id;
		$total = $commission->total - $commission->holdback;
		$commsTotal = $this->repository->findByUserId($user_id);
		if($commsTotal) {
			$commsTotal->pending -= $total;
			$commsTotal->ready += $total;
			$commsTotal->save();
		}
		return $commsTotal;
	}

	public function setHoldbackToReady(Commission $commission)
	{
		$user_id = $commission->user_id;
		$commsTotal = $this->repository->findByUserId($user_id);
		if($commsTotal) {
			$commsTotal->pending -= $commission->holdback;
			$commsTotal->ready += $commission->holdback;
			$commsTotal->save();
		}
		return $commsTotal;
	}
}