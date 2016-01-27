<?php
namespace App\Repositories;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Transaction;

class TransactionRepository extends AbstractRepository implements TransactionRepositoryInterface
{
	public function model()
	{
		return Transaction::class;
	}

	/**
	 * @param $attribute
	 * @param $value
	 * @param array $columns
	 * @return mixed
	 */
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
}