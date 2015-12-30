<?php
namespace App\Repositories;

use App\Repositories\Contracts\TransactionDetailRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\TransactionDetail;

class TransactionDetailRepository extends AbstractRepository implements TransactionDetailRepositoryInterface
{
	public function model()
	{
		return TransactionDetail::class;
	}
}