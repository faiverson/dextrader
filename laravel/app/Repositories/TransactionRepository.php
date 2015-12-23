<?php
namespace App\Repositories;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;;
use App\Models\Transaction;

class TransactionRepository extends AbstractRepository implements TransactionRepositoryInterface
{
	public function model()
	{
		return Transaction::class;
	}
}