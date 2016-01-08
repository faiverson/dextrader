<?php
namespace App\Repositories;

use App\Repositories\Contracts\SpecialOfferRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\SpecialOffer;

class SpecialOfferRepository extends AbstractRepository implements SpecialOfferRepositoryInterface
{
	public function model()
	{
		return SpecialOffer::class;
	}

	public function findByFunnel($funnel_id)
	{
		return $this->model->where('funnel_id', $funnel_id)->get();
	}

}