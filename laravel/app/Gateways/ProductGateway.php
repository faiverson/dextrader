<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Repositories\ProductRepository;
use App\Services\ProductCreateValidator;
use App\Services\ProductUpdateValidator;
use App\Models\Product;

class ProductGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(ProductRepository $repository, ProductCreateValidator $createValidator, ProductUpdateValidator $updateValidator)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
	}
	public function price(Product $product)
	{
		return $this->repository->getPrice($product);
	}

}