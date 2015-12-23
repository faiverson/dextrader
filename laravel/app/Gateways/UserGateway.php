<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\UserCreateValidator;
use App\Services\UserUpdateValidator;
use App\Repositories\UserRepository;

class UserGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(UserRepository $repository, UserCreateValidator $createValidator, UserUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function update(array $data, $id)
	{
		if($data['username']) {
			unset($data['username']);
		}

		if( ! $this->updateValidator->with($data)->passes() )
		{
			$this->errors = $this->updateValidator->errors();
			return false;
		}

		return $this->repository->update($data, $id);
	}

	public function getIdByUsername($username) {
		return $this->repository->getIdByUsername($username);
	}
}