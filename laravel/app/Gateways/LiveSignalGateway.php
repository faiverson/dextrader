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

	public function add(array $data)
	{
		$signal = $this->create($data);
		if(!$signal) {
			$this->errors = $this->errors();
			return false;
		}
		return $signal;
	}

	public function change(array $data, $id)
	{
		$signal = $this->update($data, $id);
		if(!$signal) {
			$this->errors = $this->errors();
			return false;
		}
		return $signal;
	}

}