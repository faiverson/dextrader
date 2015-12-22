<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\UserCreateValidator;
use App\Services\UserUpdateValidator;
use App\Repositories\UserRepository;

class CardGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(CardRepository $repository, UserCreateValidator $createValidator, UserUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

}