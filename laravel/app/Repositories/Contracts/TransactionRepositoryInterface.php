<?php namespace App\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
/**
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface TransactionRepositoryInterface extends RepositoryInterface {
	public function findWith($id);
	public function refund($id);
	public function showUserTransactions($id, $limit, $offset, $order_by, $filters);
	public function showTotalUserTransactions($id, $filters);
}