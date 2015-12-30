<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\SubscriptionCreateValidator;
use App\Services\SubscriptionUpdateValidator;
use App\Repositories\SubscriptionRepository;

class SubscriptionGateway extends AbstractGateway
{

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(SubscriptionRepository $repository, SubscriptionCreateValidator $createValidator, SubscriptionUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function findByUser($user_id, $columns = array('*'), $limit = null, $offset = null)
	{
		return $this->repository->findByUser($user_id, $columns, $limit, $offset);
	}

	public function isOwner($user_id, $subscription_id)
	{
		return $this->repository->isOwner($user_id, $subscription_id);
	}


}