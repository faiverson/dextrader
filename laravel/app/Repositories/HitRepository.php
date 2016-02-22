<?php
namespace App\Repositories;

use App\Repositories\Contracts\HitRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Hit;

class HitRepository extends AbstractRepository implements HitRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return Hit::class;
	}

	public function findMore($user_id, $funnel_id, $tag_id, $ip_address)
	{
		return $this->model->where('enroller_id', $user_id)
			->where('funnel_id', $funnel_id)
			->where('tag_id', $tag_id)
			->where('ip_address', $ip_address)
			->get();
	}

}