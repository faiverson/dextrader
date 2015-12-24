<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\PurchaseCreateValidator;
use App\Services\PurchaseUpdateValidator;
use App\Repositories\PurchaseRepository;

class PurchaseGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(PurchaseRepository $repository, PurchaseCreateValidator $createValidator, PurchaseUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}
}