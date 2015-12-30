<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\LiveSignalCreateValidator;
use App\Services\LiveSignalUpdateValidator;
use App\Repositories\LiveSignalRepository;

class LiveSignalGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(LiveSignalRepository $repository, LiveSignalCreateValidator $createValidator, LiveSignalUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}
}