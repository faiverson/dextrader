<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Repositories\SpecialOfferRepository;
use App\Services\SpecialOfferCreateValidator;
use App\Services\SpecialOfferUpdateValidator;

class SpecialOfferGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(SpecialOfferRepository $repository, SpecialOfferCreateValidator $createValidator, SpecialOfferUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

	public function findByFunnel($page)
	{
		return $this->repository->findByFunnel($page);
	}

	public function findIn($ids)
	{
		return $this->repository->findIn($ids);
	}
}