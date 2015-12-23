<?php
namespace App\Repositories;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Product;

class ProductRepository extends AbstractRepository implements ProductRepositoryInterface
{


	// This is where the "magic" comes from:
	public function model()
	{
		return Product::class;
	}

	public function getPrice(Product $product)
	{
		return number_format($product->amount - ($product->amount * $product->discount), 2, '.', ',');
	}
}