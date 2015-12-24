<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\SubscriptionCreateValidator;
use App\Services\SubscriptionUpdateValidator;
use App\Repositories\SubscriptionRepository;

class SubscriptionGateway extends AbstractGateway {

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
}