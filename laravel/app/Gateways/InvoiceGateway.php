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

	protected $detail;

	protected $errors;

	public function __construct(InvoiceDetailGateway $detail, InvoiceRepository $repository, InvoiceCreateValidator $createValidator, InvoiceUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
		$this->detail = $detail;
	}

	public function add(array $data)
	{
		foreach($data['products'] as $product) {
			$this->detail->create($product);
		}

		return $this->repository->create($data);
	}

	public function addDetail(array $data)
	{
		$detail =  $this->detail->create($data);
		if(!$detail) {
			$this->errors = $this->detail->errors();
		}
		return $detail;
	}

	public function findBy($attribute, $value, $columns = array('*'), $limit = null, $offset = null, $order_by = null) {
		return $this->repository->findBy($attribute, $value, $columns, $limit, $offset, $order_by);
	}
}