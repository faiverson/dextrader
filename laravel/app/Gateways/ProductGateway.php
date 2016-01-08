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

	public function total(Collection $products)
	{
		$total = 0;
		foreach($products as $product) {
			$total += $this->repository->getPrice($product);
		}
		return $total;
	}
	public function getPrice(Product $product)
	{
		return $this->repository->getPrice($product);
	}


	public function UserCanBuy(Collection $sub, Collection $products)
	{
		$ids = array_column($products->toArray(), 'product_id');
		$subs = array_column($sub->toArray(), 'product_id');
		foreach($products as $product) {
			if(in_array($product->id, $subs)) {
				$this->errors = ['You have the product ' . $product->display_name . ' already!'];
				return false;
			}
			if($product->parents == 0) {
				continue;
			} else {
				$parents = explode(',', $product->parents);
				$result = array_diff($parents, array_merge($subs, $ids));
				if(count($result) > 0) {
					$this->errors = ['You need to buy or activate other product/s first to unlock this product'];
					return false;
				}
			}
		}

		return true;
	}

	public function findIn(array $products)
	{
		return $this->repository->findIn($products)->where('active', 1);
	}

}