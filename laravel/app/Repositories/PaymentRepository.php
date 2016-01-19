<?php
namespace App\Repositories;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Payment;

class PaymentRepository extends AbstractRepository implements PaymentRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return Payment::class;
	}

	public function findLastByUser($user_id)
	{
		return $this->model->where('user_id', $user_id)->orderBy('id', 'desc')->first();
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
	public function getUserPayments($id, $limit, $offset, $order_by, $where)
	{
		$query =  $this->model;
		$query = $query->where('user_id', $id);

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

		return $query->get();
	}

	public function getTotalUserPayments($id, $where)
	{
		$query =  $this->model;
		$query = $query->where('user_id', $id);

		if(array_key_exists('from', $where) && $where['from'] != null) {
			$from = new DateTime($where['from']);
			$query = $query->whereDate('created_at', '>=', $from);
		}

		if(array_key_exists('to', $where) && $where['to'] != null) {
			$from = new DateTime($where['to']);
			$query = $query->whereDate('created_at', '<=', $from);
		}

		return $query->count();
	}

}