<?php
namespace App\Gateways;

use App\Repositories\TransactionDetailRepository;
use App\Services\TransactionDetailCreateValidator;
use App\Services\TransactionDetailUpdateValidator;
use App\Gateways\AbstractGateway;

class TransactionDetailGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(TransactionDetailRepository $repository,
								TransactionDetailCreateValidator $transactionCreateValidator,
								TransactionDetailUpdateValidator $transactionUpdateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $transactionCreateValidator;
		$this->updateValidator = $transactionUpdateValidator;
	}
}