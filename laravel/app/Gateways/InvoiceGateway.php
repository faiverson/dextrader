<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\InvoiceCreateValidator;
use App\Services\InvoiceUpdateValidator;
use App\Repositories\InvoiceRepository;

class InvoiceGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(InvoiceRepository $repository, InvoiceCreateValidator $createValidator, InvoiceUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}
}