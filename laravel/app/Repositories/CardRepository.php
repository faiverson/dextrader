<?php
namespace App\Repositories;

use App\Repositories\Contracts\CardRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;;
use CreditCard;

class CardRepository extends AbstractRepository implements CardRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return CreditCard::class;
	}

	public function findById($id, $column = 'id', $columns = array('*')) {
		$this->model = $this->model->with('roles')->where('active', 1)->where($column, $id);

		return $this->model->select($columns)->first();
	}

}