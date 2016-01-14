<?php
namespace App\Repositories;

use App\Repositories\Contracts\LiveSignalRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\NASignal;

class NASignalRepository extends AbstractRepository implements LiveSignalRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return NASignal::class;
	}

	public function find_signal($mt_id, $trade)
	{
		return $this->model->where('mt_id', $mt_id)->where('trade_type', $trade)->first(['id']);
	}
}