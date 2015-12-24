<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\BillingAddressCreateValidator;
use App\Services\BillingAddressUpdateValidator;
use App\Repositories\BillingAddressRepository;

class BillingAddressGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(BillingAddressRepository $repository, BillingAddressCreateValidator $createValidator, BillingAddressUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}
}