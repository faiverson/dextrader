<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\InvoiceDetailCreateValidator;
use App\Services\InvoiceDetailUpdateValidator;
use App\Repositories\InvoiceDetailRepository;

class InvoiceDetailGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(InvoiceDetailRepository $repository, InvoiceDetailCreateValidator $createValidator, InvoiceDetailUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}

}