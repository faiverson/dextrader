<?php
namespace App\Repositories;

use App\Repositories\Contracts\PurchaseRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;;
use Purchase;

class PurchaseRepository extends AbstractRepository implements PurchaseRepositoryInterface
{
	public function model()
	{
		return Purchase::class;
	}
}