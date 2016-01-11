<?php
namespace App\Repositories;

use App\Repositories\Contracts\CommissionRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Commission;
use DateTime;
use DB;

class CommissionRepository extends AbstractRepository implements CommissionRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return Commission::class;
	}

	public function getIdByCommission($tag) {
		$this->model = $this->model->where('tag', $tag);
		$t = $this->model->select(['id'])->first();
		return $t != null ? $t->id : null;
	}

	public function getCommissionToPay()
	{
		// we set sunday as the first day of this week
		$from = new DateTime('last sunday');
		$to = new DateTime('today');
		$query = $this->model
			->select([
				DB::raw('GROUP_CONCAT(id SEPARATOR ",") AS ids'),
				DB::raw('to_user_id AS user_id'),
				DB::raw('SUM(amount) AS total'),
				DB::raw('(SUM(amount) / 10) AS holdback')
			])
			->WhereNull('payout_dt')
			->WhereNull('refund_dt')
//			->whereDate('created_at', '>=', $from)
			->whereDate('created_at', '<=', $to)
			->groupBy('to_user_id')
			->having('total', '>', 20);
		return $query->get();
	}

}