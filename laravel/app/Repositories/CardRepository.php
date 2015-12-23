<?php
namespace App\Repositories;

use App\Repositories\Contracts\CardRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;;
use CreditCard;

class CardRepository extends AbstractRepository implements CardRepositoryInterface
{
	public function model()
	{
		return CreditCard::class;
	}
}