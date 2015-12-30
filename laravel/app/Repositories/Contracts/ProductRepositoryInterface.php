<?php namespace App\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
use App\Models\Product;
/**
 * The UserRepositoryInterface contains ONLY method signatures for methods
 * related to the User object.
 *
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface ProductRepositoryInterface extends RepositoryInterface {
	public function getPrice(Product $product);
	public function findIn(array $products);
}