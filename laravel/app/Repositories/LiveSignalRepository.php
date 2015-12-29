<?php
namespace App\Repositories;

use App\Repositories\Contracts\LiveSignalRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\LiveSignal;

class LiveSignalRepository extends AbstractRepository implements LiveSignalRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return LiveSignal::class;
	}
}