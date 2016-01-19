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
	 * Find which pendings comms are ready to
	 * change to 'ready' status
	 * all commissions are ready after 2 weeks of since
	 * the transaction/sale was made
	 *
	 */
	public function getPendingToReady($limit = 50)
	{
		// we set sunday as the first day of this week
		$from = new DateTime('today');
		$from->modify('-2 weeks');
		// After 3 months the holdback can be collected
		$holdback_dt = new DateTime('today');
		$holdback_dt->modify('-3 months');

		$query = $this->model
			->select([
				DB::raw('GROUP_CONCAT(id SEPARATOR ",") AS ids'),
				DB::raw('GROUP_CONCAT(CASE WHEN holdback_dt IS NULL THEN id END SEPARATOR \',\') AS comms_ids'),
				DB::raw('GROUP_CONCAT(CASE WHEN holdback_dt IS NOT NULL THEN id END SEPARATOR \',\') AS holdbacks_ids'),
				DB::raw('to_user_id AS user_id'),
				DB::raw('SUM( CASE WHEN holdback_dt IS NULL THEN (amount - holdback) ELSE holdback END ) AS total'),
				DB::raw('SUM( CASE WHEN holdback_dt IS NULL THEN (amount - holdback) ELSE 0 END ) AS total_comms'),
				DB::raw('SUM( CASE WHEN holdback_dt IS NOT NULL THEN holdback ELSE 0 END ) AS total_holdbacks')
			])
			->where(function ($q) use ($from) {
				$q->where('status', 'pending')
					->WhereNull('payout_dt')
					->WhereNull('refund_dt')
					->whereDate('created_at', '<=', $from);
			})
			->orWhere(function ($q) use ($holdback_dt) {
				$q->where('status', 'ready')
					->where('holdback_paid', 0)
					->WhereNull('holdback_dt')
					->whereDate('created_at', '<=', $holdback_dt);
			})
			->groupBy('to_user_id')
			->take($limit);
		return $query->get();
	}

	public function getCommissionToPay()
	{
		$today = new DateTime('now');
		$today = new DateTime('2016-01-22 12:00:00');  // @TODO remove
		$today = $today->format('Y-m-d');
		$query = $this->model
			->select([
				DB::raw('GROUP_CONCAT(id) AS ids'),
				DB::raw('GROUP_CONCAT(CASE WHEN holdback_dt IS NULL THEN id END SEPARATOR \',\') AS comms_ids'),
				DB::raw('GROUP_CONCAT(CASE WHEN holdback_dt IS NOT NULL THEN id END SEPARATOR \',\') AS holdbacks_ids'),
				DB::raw('to_user_id AS user_id'),
				DB::raw('SUM( CASE WHEN holdback_dt IS NULL THEN (amount - holdback) ELSE holdback END ) AS total')
			])
			->where(function ($q) use ($today) {
				$q->where(DB::raw('DATE(payout_dt)'), $today)
					->where('status', 'ready')
					->whereNull('refund_dt');
			})
			->orWhere(function ($q) use ($today) {
				$q->where(DB::raw('DATE(holdback_dt)'), $today)
					->whereNull('refund_dt')
					->whereIn('status', ['ready', 'paid'])
					->where('holdback_paid', 0);
			})
			->groupBy('to_user_id');

		return $query->get();
	}

	/**
	 * @param $ids of commissions
	 * @return mixed
	 */
	public function updateToReady($ids)
	{
		if(!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		$now = $this->takePaymentDay();
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
		$now = $this->takePaymentDay();
		return $this->model->whereIn('id', $ids)->update([
			'holdback_dt' => $now->format('Y-m-d H:i:s')
		]);
	}

	/**
	 * @param $ids of commissions
	 * @return mixed
	 */
	public function updateToPaid($ids)
	{
		if(!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		return $this->model->whereIn('id', $ids)->update([
			'status' => 'paid'
		]);
	}

	public function updateHoldbackToPaid($ids)
	{
		if(!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		return $this->model->whereIn('id', $ids)->update([
			'holdback_paid' => 1
		]);
	}

	public function payCommissionOnNextDate($ids)
	{
		$date = $this->nextPaymentDay();

		if(!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		return $this->model->whereIn('id', $ids)->update([
			'payout_dt' => $date->format('Y-m-d H:i:s')
		]);
	}

	/**
	 * We change the date where the holdback are set to ready status
	 *
	 * @param $ids
	 * @return mixed
	 */
	public function payHoldbacksOnNextDate($ids)
	{
		$date = $this->nextPaymentDay();
		if(!is_array($ids)) {
			$ids = explode(',', $ids);
		}
		return $this->model->whereIn('id', $ids)->update([
			'holdback_dt' => $date->format('Y-m-d H:i:s')
		]);
	}

	/**
	 * Show all the commissions that belong to a user
	 *
	 * @param $id
	 * @param $limit
	 * @param $offset
	 * @param $order_by
	 * @param $where
	 * @return mixed
	 */
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

	public function getTotalUserCommissions($id, $where)
	{
		$query =  $this->model;

		$query = $query->with('from')->with('products')->where('to_user_id', $id);


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

		return $query->count();
	}

	/**
	 * We calculate when is the following date
	 * where we make the payments
	 *
	 * @return DateTime
	 */
	protected function takePaymentDay()
	{
		$now = new DateTime('now');
		if($now->format('l') !== 'friday') {
			$now->modify('next friday');
		}
		return $now;
	}

	/**
	 * We set the next date where we set the payments
	 *
	 * @return DateTime
	 */
	protected function nextPaymentDay()
	{
		$now = new DateTime('now');
		$now->modify('+1 week');
		return $now;
	}

}