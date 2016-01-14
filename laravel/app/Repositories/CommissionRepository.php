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

	/**
	 * take the pending comms they are ready
	 * find all commissions with 2 weeks of time
	 *
	 */
	public function getPendingToReady($limit = 50)
	{
		// we set sunday as the first day of this week
		$from = new DateTime('today');
		$from->modify('-2 weeks');

		$query = $this->model
			->select([
				DB::raw('GROUP_CONCAT(id SEPARATOR ",") AS ids'),
				DB::raw('to_user_id AS user_id'),
				DB::raw('SUM(amount) AS total'),
				DB::raw('TRUNCATE((SUM(amount) / 10), 2) AS holdback')
			])
			->where('status', 'pending')
			->WhereNull('payout_dt')
			->WhereNull('refund_dt')
			->whereDate('created_at', '<=', $from)
			->groupBy('to_user_id')
			->take($limit);
		return $query->get();
	}

	public function getPendingHoldbakToReady($limit = 50)
	{
		// we set sunday as the first day of this week
		$holdback_dt = new DateTime('today');
		$holdback_dt->modify('-3 months');

		$query = $this->model
			->select([
				DB::raw('GROUP_CONCAT(id SEPARATOR ",") AS ids'),
				DB::raw('to_user_id AS user_id'),
				DB::raw('TRUNCATE((SUM(amount) / 10), 2) AS holdback')
			])
			->where('status', 'ready')
			->where('holdback_paid', 0)
			->WhereNull('holdback_dt')
			->whereDate('created_at', '<=', $holdback_dt)
			->groupBy('to_user_id')
			->take($limit);
		return $query->get();
	}

	public function updateToReady($ids)
	{
		if(!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		$now = new DateTime('now');
		return $this->model->whereIn('id', $ids)->update([
			'status' => 'ready',
			'payout_dt' => $now->format('Y-m-d H:i:s')
		]);
	}

	public function updateHoldbackToReady($ids)
	{
		if(!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		$now = new DateTime('now');
		return $this->model->whereIn('id', $ids)->update([
			'holdback_dt' => $now->format('Y-m-d H:i:s')
		]);
	}

	public function getCommissionToPay()
	{
		// we set sunday as the first day of this week
		$query = $this->model
			->select([
				DB::raw('GROUP_CONCAT(id SEPARATOR ",") AS ids'),
				DB::raw('to_user_id AS user_id'),
				DB::raw('SUM(amount) AS total'),
				DB::raw('TRUNCATE((SUM(amount) / 10), 2) AS holdback')
			])
			->where('status', 'ready')
			->groupBy('to_user_id')
			->having('total', '>+' ,'20');
		return $query->get();
	}

	public function getUserCommissions($id, $limit, $offset, $order_by, $where)
	{
		$query =  $this->model;

		$query = $query->with('from')->with('products')->where('to_user_id', $id);

		if($limit != null) {
			$query = $query->take($limit);
		}

		if($offset != null) {
			$query = $query->skip($offset);
		}

		if($order_by != null) {
			foreach($order_by as $column => $dir) {
				$query = $query->orderBy($column, $dir);
			}
		}

		if(array_key_exists('from', $where) && $where['from'] != null) {
			$from = new DateTime($where['from']);
			$query = $query->whereDate('created_at', '>=', $from);
		}

		if(array_key_exists('to', $where) && $where['to'] != null) {
			$from = new DateTime($where['to']);
			$query = $query->whereDate('created_at', '<=', $from);
		}

		if(array_key_exists('status', $where) && $where['status'] != null) {
			$query = $query->where('status', '=', $where['status']);
		}

		if(array_key_exists('product', $where) && $where['product'] != null) {
			$query = $query->whereHas('products', function($q) use ($where) {
				$q->where('invoices_detail.product_display_name', '=', $where['product']);
			});
		}

		return $query->get();
	}



}