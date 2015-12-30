<?php namespace App\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
/**
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface TransactionRepositoryInterface extends RepositoryInterface {
	public function findWith($id);
}