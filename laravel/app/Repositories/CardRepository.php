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

	public function isCard($number, $user_id)
	{
		return CreditCard::where('number', Encrypt::encrypt($number))->where('user_id', '!=', $user_id)->count();
	}

	public function findUserCard($user_id, $card_id)
	{
		return $this->model->where('id', $card_id)->where('user_id', $user_id)->first();
	}
}