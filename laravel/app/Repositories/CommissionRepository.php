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
				DB::raw('GROUP_CONCAT(CASE WHEN payout_dt IS NULL AND refund_dt IS NULL AND status = \'pending\' THEN id END SEPARATOR \',\') AS comms_ids'),
				DB::raw('GROUP_CONCAT(CASE WHEN holdback_dt IS NULL AND holdback_paid = 0 AND status IN(\'ready\', \'paid\') THEN id END SEPARATOR \',\') AS holdbacks_ids'),
				DB::raw('to_user_id AS user_id'),
				DB::raw('SUM( CASE WHEN payout_dt IS NULL  THEN (amount - holdback) ELSE holdback END ) AS total'),
				DB::raw('SUM( CASE WHEN payout_dt IS NULL THEN (amount - holdback) ELSE 0 END ) AS total_comms'),
				DB::raw('SUM( CASE WHEN payout_dt IS NOT NULL AND refund_dt IS NULL THEN holdback ELSE 0 END ) AS total_holdbacks')
			])
			->where(function ($q) use ($from) {
				$q->where('status', 'pending')
					->WhereNull('payout_dt')
					->WhereNull('refund_dt')
					->whereDate('created_at', '<=', $from);
			})
			->orWhere(function ($q) use ($holdback_dt) {
				$q->whereIn('status', ['ready', 'paid'])
					->where('holdback_paid', 0)
					->WhereNull('holdback_dt')
					->WhereNull('refund_dt')
					->whereDate('created_at', '<=', $holdback_dt);
			})
			->groupBy('to_user_id')
			->take($limit);
		return $query->get();
	}

	public function getCommissionToPay(DateTime $date)
	{
		$query = $this->model
			->has('active') // check if the user is active
			->with('to') // add user info
			->select([
				'*',
				DB::raw('GROUP_CONCAT(id) AS ids'),
				DB::raw('GROUP_CONCAT(CASE WHEN holdback_dt IS NULL THEN id END SEPARATOR \',\') AS comms_ids'),
				DB::raw('GROUP_CONCAT(CASE WHEN holdback_dt IS NOT NULL THEN id END SEPARATOR \',\') AS holdbacks_ids'),
				DB::raw('to_user_id AS user_id'),
				DB::raw('SUM( CASE WHEN holdback_dt IS NULL THEN (amount - holdback) ELSE holdback END ) AS total')
			])
			->where(function ($q) use ($date) {
				$q->whereDate('payout_dt', '=', $date->format('Y-m-d'))
					->where('status', 'ready')
					->whereNull('refund_dt');
			})
			->orWhere(function ($q) use ($date) {
				$q->where(DB::raw('DATE(holdback_dt)'), $date->format('Y-m-d'))
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
		$select = [
			'to_user_id',
			'from_user_id',
			'status',
			'amount',
			'holdback',
			'invoice_id',
			'created_at'
		];

		$query_enroller =  $this->model->with('to')->with('from')->with('products')->select($select);
		$query_enroller = $query_enroller->where('to_user_id', $id)->where('type', 'enroller');
		$query_enroller = $this->filters($query_enroller, $where);

		$select = [
			'c.to_user_id',
			'c.from_user_id',
			'commissions.status',
			'commissions.amount',
			'commissions.holdback',
			'commissions.invoice_id',
			'commissions.created_at'
		];
		$query_parent =  $this->model->with('to')->with('from')->with('products')->select($select);
		$query_parent = $query_parent->join('commissions AS c', function($join) {
				$join->on('c.invoice_id', '=', 'commissions.invoice_id')
					->on('c.to_user_id', '=', 'commissions.from_user_id');
			})->where('commissions.to_user_id', $id)->where('commissions.type', 'parent');
		$query_parent = $this->filters($query_parent, $where);
		$query = $query_enroller->union($query_parent);

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

		return $query->get();
	}

	public function getTotalUserCommissions($id, $where)
	{
		$params = array_merge([$id, 'enroller'], array_values($where), [$id, 'parent'], array_values($where));
		$query_enroller =  $this->model->with('to')->with('from')->with('products')->select(['to_user_id', 'from_user_id', 'status', 'amount', 'holdback', 'invoice_id', 'created_at']);
		$query_enroller = $query_enroller->where('to_user_id', $id)->where('type', 'enroller');
		$query_enroller = $this->filters($query_enroller, $where);

		$query_parent =  $this->model->with('to')->with('from')->with('products')->select(['c.to_user_id', 'c.from_user_id', 'status', 'amount', 'holdback', 'invoice_id', 'created_at']);
		$query_parent = $query_parent->join('commissions AS c', function($join) {
			$join->on('c.invoice_id', '=', 'commissions.invoice_id')
				->on('c.to_user_id', '=', 'commissions.from_user_id');
		})->where('commissions.to_user_id', $id)->where('commissions.type', 'parent');
		$query_parent = $this->filters($query_parent, $where);

		$query = $query_enroller->union($query_parent);
		$query = DB::select('SELECT count(*) as total from (' . $query->toSql() . ') as totals', $params);
		return $query[0]->total;
	}

	protected function filters($query, $where)
	{
		foreach($where as $key => $w) {
			switch($key) {
				case 'from':
					$from = new DateTime($w);
					$query = $query->whereDate('commissions.created_at', '>=', $from);
					break;
				case 'to':
					$from = new DateTime($w);
					$query = $query->whereDate('commissions.created_at', '<=', $from);
					break;
				case 'status':
					$query = $query->where('commissions.status', $w);
					break;
				case 'product':
					$query = $query->whereHas('products', function($q) use ($w) {
						$q->where('invoices_detail.product_display_name', '=', $w['product']);
					});
					break;
			}
		}
		return $query;
	}

	public function getSummaryUserCommissions($id)
	{
		$query =  $this->model;
		$from = new DateTime('yesterday');
		$yesterday = $from->format('Y-m-d');
		$today = $from->modify('+1 day')->format('Y-m-d');
		$week = $from->modify('-1 week')->format('Y-m-d');
		$month = $from->modify('+1 week')->modify('-1 month')->format('Y-m-d');
		$year = $from->modify('+1 month')->modify('-1 year')->format('Y-m-d');

		// creating the query
		$query = $query->where('to_user_id', $id);
		$query->groupBy('to_user_id');
		return $query->select([
			DB::raw("(SELECT SUM(c1.amount) FROM commissions c1 WHERE DATE(created_at) = '{$yesterday}' AND c1.to_user_id = commissions.to_user_id) AS yesterday"),
			DB::raw("(SELECT SUM(c2.amount) FROM commissions c2 WHERE DATE(created_at) = '{$today}' AND c2.to_user_id = commissions.to_user_id) AS today"),
			DB::raw("(SELECT SUM(c3.amount) FROM commissions c3 WHERE DATE(created_at) >= '{$week}' AND c3.to_user_id = commissions.to_user_id) AS last_week"),
			DB::raw("(SELECT SUM(c4.amount) FROM commissions c4 WHERE DATE(created_at) >= '{$month}' AND c4.to_user_id = commissions.to_user_id) AS last_month"),
			DB::raw("(SELECT SUM(c5.amount) FROM commissions c5 WHERE DATE(created_at) >= '{$year}' AND c5.to_user_id = commissions.to_user_id) AS last_year"),
			DB::raw("(SELECT (paid + pending + ready) FROM commissions_total WHERE user_id = commissions.to_user_id) AS all_time"),
		])->first();
	}

	public function findByInvoice($invoice_id)
	{
		$query =  $this->model;
		return $query->where('invoice_id', $invoice_id)->get();
	}

	public function findUserCommisionByInvoice($invoice_id)
	{
		$query =  $this->model;
		return $query->where('invoice_id', $invoice_id)->where('type', 'enroller')->first();
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