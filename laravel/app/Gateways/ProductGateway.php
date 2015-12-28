<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Repositories\ProductRepository;
use App\Services\ProductCreateValidator;
use App\Services\ProductUpdateValidator;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

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

	public function UserCanBuy(Collection $sub, Product $product)
	{
		if($product->parents == 0) {
			return true;
		} else {
			$parents = explode(',', $product->parents);
			$subs = array_column($sub->toArray(), 'product_id');
			$result = array_diff($parents, $subs);
			if(count($result) > 0) {
				$this->errors = ['You need to buy other packages first to unlock this product'];
				return false;
			}
		}

		return true;
	}

}