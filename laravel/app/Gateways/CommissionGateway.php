<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\CommissionCreateValidator;
use App\Services\CommissionUpdateValidator;
use App\Repositories\CommissionRepository;

class CommissionGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(CommissionRepository $repository, CommissionCreateValidator $createValidator, CommissionUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}
}