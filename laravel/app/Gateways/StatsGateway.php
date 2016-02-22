<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Repositories\StatRepository;
use App\Services\StatCreateValidator;
use App\Services\StatUpdateValidator;

class StatsGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(StatRepository $repository, StatCreateValidator $createValidator, StatUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function getMarketingStats($user_id, $limit, $offset, $order_by, $where) {
		return $this->repository->getMarketingStats($user_id, $limit, $offset, $order_by, $where);
	}

	public function getTotalMarketingStats($user_id, $where) {
		return $this->repository->getTotalMarketingStats($user_id, $where);
	}

	public function findByUser($user_id, $funnel_id, $tag_id, $created_at)
	{
		return $this->repository->findByUser($user_id, $funnel_id, $tag_id, $created_at);
	}

}