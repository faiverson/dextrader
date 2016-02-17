<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\LeadCreateValidator;
use App\Services\LeadUpdateValidator;
use App\Repositories\LeadRepository;

class LeadGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(LeadRepository $repository, LeadCreateValidator $createValidator, LeadUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function findByEmail($email)
	{
		return $this->repository->findByEmail($email);
	}
}