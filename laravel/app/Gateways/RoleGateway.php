<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\RoleCreateValidator;
use App\Services\RoleUpdateValidator;
use App\Repositories\RoleRepository;

class RoleGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(RoleRepository $repository, RoleCreateValidator $createValidator, RoleUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function getRoleIdByName($name) {
		return $this->repository->getRoleIdByName($name);
	}
}