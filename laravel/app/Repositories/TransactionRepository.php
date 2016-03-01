<?php
namespace App\Repositories;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Transaction;
use DB;
use DateTime;

class TransactionRepository extends AbstractRepository implements TransactionRepositoryInterface
{
	public function model()
	{
		return Transaction::class;
	}

	public function findBy($attribute, $value, $columns = array('*'), $limit = null, $offset = null, $order_by = null) {
		$query = $this->model;
		if($limit != null) {
			$query = $this->model->take($limit);
		}

		if($offset != null) {
			$query = $query->skip($offset);
		}

		if($order_by != null) {
			foreach($order_by as $column => $dir) {
				$query = $query->orderBy($column, $dir);
			}
		}
		return $query->with('detail')->where($attribute, '=', $value)->get($columns);
	}

	public function findWith($id) {
		$query = $this->model;
		return $query->with('detail')->find($id);
	}

	public function refund($id) {
		$query = $this->model;
		return $query->find($id)->update();
	}

	public function showUserTransactions($user_id, $limit, $offset, $order_by, $where)
	{
		$query =  $this->model->where('user_id', $user_id);
		$query =  $query->select(['*', DB::raw('(SELECT COUNT(orderid) FROM transactions t WHERE t.type = "refund" AND t.orderid = transactions.orderid) as refunded')]);
		$query = $this->filters($query, $where);
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

	public function showTotalUserTransactions($user_id, $where)
	{
		$query =  $this->model->where('user_id', $user_id);
		$query = $this->filters($query, $where);
		return $query->count();
	}

	protected function filters($query, $where)
	{
		foreach($where as $key => $w) {
			switch($key) {
				case 'from':
					$from = new DateTime($w);
					$query = $query->whereDate('created_at', '>=', $from);
					break;
				case 'to':
					$from = new DateTime($w);
					$query = $query->whereDate('created_at', '<=', $from);
					break;
				case 'type':
					$query = $query->where('type', $w);
					break;
			}
		}

		return $query;
	}
}