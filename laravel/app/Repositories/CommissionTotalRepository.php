<?php
namespace App\Repositories;

use App\Repositories\Contracts\CommissionTotalRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\CommissionTotal;

class CommissionTotalRepository extends AbstractRepository implements CommissionTotalRepositoryInterface
{
	public function model()
	{
		return CommissionTotal::class;
	}

	public function findByUserId($user_id)
	{
		return $this->model->where('user_id', $user_id)->first();
	}
}