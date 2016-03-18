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

	protected $productGateway;

	protected $errors;

	public function __construct(SpecialOfferRepository $repository, SpecialOfferCreateValidator $createValidator, SpecialOfferUpdateValidator $updateValidator, ProductGateway $productGateway)
	{
		$this->repository = $repository;
		$this->createValidator = $createValidator;
		$this->updateValidator = $updateValidator;
		$this->product = $productGateway;
	}

	public function findByFunnel($page)
	{
		$offers = $this->repository->findByFunnel($page);
		if($offers->count() > 0) {
			$ids = array_unique(array_column($offers->toArray(), 'product_id'));
			$products= $this->product->findIn($ids);
		}

		return [
			'products' => $products,
			'offers' => $offers
		];
	}

	public function findIn($ids)
	{
		$ids = is_array($ids) ? $ids : [$ids];
		return $this->repository->findIn($ids);
	}
}