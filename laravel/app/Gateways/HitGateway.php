<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\HitUpdateValidator;
use App\Services\HitCreateValidator;
use App\Repositories\HitRepository;

class HitGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(HitRepository $repository, HitCreateValidator $createValidator, HitUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

}