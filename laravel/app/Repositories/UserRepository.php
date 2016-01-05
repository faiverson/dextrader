<?php
namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use User;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return User::class;
	}

	public function actives($columns = array('*'), $limit = null, $offset = null, $order_by = null) {
		$user = $this->model->with('roles')->where('active', 1);
		$sortColumns = ['id', 'username', 'last_name', 'email'];

		if($limit != null) {
			$user = $user->take($limit);
		}

		if($offset != null) {
			$user = $user->skip($offset);
		}

		if($order_by != null) {
			foreach($order_by as $column => $dir) {
				$user = $user->orderBy($sortColumns[$column], $column['dir']);
			}
		}

		return $user->get($columns);
	}

	public function findById($id, $column = 'id', $columns = array('*')) {
		$user = $this->model->with('roles')->where('active', 1)->where($column, $id);
		return $user->select($columns)->first();
	}

	public function findByUsername($username, $columns = array('*')) {
		$user = $this->model->with('roles')->where('active', 1)->where('username', $username);
		return $user->select($columns)->first();
	}

	public function getIdByUsername($username) {
		$user = $this->model->with('roles')->where('active', 1)->where('username', $username);
		$user = $user->select(['id'])->first();
		return $user != null ? $user->id : null;
	}

	public function addRole($user_id, $role_id)
	{
		return $this->model->find($user_id)->attachRole($role_id);
	}

	public function detachRole($user_id, $role_id)
	{
		return $this->model->find($user_id)->detachRole($role_id);
	}
}