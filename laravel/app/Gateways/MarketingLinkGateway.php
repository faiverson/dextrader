<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Repositories\MarketingLinkRepository;
use App\Gateways\ProductGateway;
use App\Services\MarketingLinkCreateValidator;
use App\Services\MarketingLinkUpdateValidator;

class MarketingLinkGateway extends AbstractGateway {

	protected $repository;

	protected $createValidator;

	protected $updateValidator;

	protected $errors;

	public function __construct(MarketingLinkRepository $repository, MarketingLinkCreateValidator $createValidator, MarketingLinkUpdateValidator $updateValidator, ProductGateway $productGateway)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
		$this->productGateway = $productGateway;
	}

	public function getProducts($funnel_id)
	{
		$mk = $this->repository->find($funnel_id);
		if(isset($mk['products'])){
			$products = explode(',', $mk->products);
			return $this->productGateway->findIn($products);
		}
		return false;
	}

}