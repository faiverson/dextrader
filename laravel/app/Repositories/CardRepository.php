<?php
namespace App\Repositories;

use App\Repositories\Contracts\CardRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\CreditCard;
use Encrypt;

class CardRepository extends AbstractRepository implements CardRepositoryInterface
{
	public function model()
	{
		return CreditCard::class;
	}

	public function isCard($number)
	{
		return CreditCard::where('number', Encrypt::encrypt($number))->count();
	}
}