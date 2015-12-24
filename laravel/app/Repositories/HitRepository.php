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

}