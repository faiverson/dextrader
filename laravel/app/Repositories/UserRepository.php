<?php
namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;;
use User;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return User::class;
	}

	public function actives($columns = array('*'), $limit = null, $offset = null, $order_by = null) {
		$this->model = $this->model->with('roles')->where('active', 1);

		if($limit != null) {
			$this->model = $this->model->take($limit);
		}

		if($offset != null) {
			$this->model = $this->model->skip($offset);
		}

		if($order_by != null) {
			foreach($order_by as $column => $dir) {
				$this->model = $this->model->orderBy($column, $dir);
			}
		}

		return $this->model->get($columns);
	}

	public function findById($id, $column = 'id', $columns = array('*')) {
		$this->model = $this->model->with('roles')->where('active', 1)->where($column, $id);

		return $this->model->select($columns)->first();
	}

}