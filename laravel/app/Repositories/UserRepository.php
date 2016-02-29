<?php
namespace App\Repositories;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use User;
use DateTime;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
	public function model()
	{
		return User::class;
	}

	public function actives($columns = array('*'), $limit = null, $offset = null, $order_by = null, $filters = null) {
		$query = $this->model->with('roles')->where('active', 1);
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

		$query = $this->filters($query, $filters);
		return $query->get($columns);
	}

	public function total($filters = null) {
		$query = $this->model->where('active', 1);
		$query = $this->filters($query, $filters);
		return $query->count();
	}

	public function getUserBySponsor($enroller_id, $columns = array('*'), $limit = null, $offset = null, $order_by = null, $filters = null) {
		$query = $this->model->with('roles')->where('active', 1)->where('enroller_id', $enroller_id);
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

		$query = $this->filters($query, $filters);
		return $query->get($columns);
	}

	public function getTotalUserBySponsor($enroller_id, $filters = null) {
		$query = $this->model->where('active', 1)->where('enroller_id', $enroller_id);
		$query = $this->filters($query, $filters);
		return $query->count();
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

	public function addRoles($user_id, $roles)
	{
		return $this->model->find($user_id)->attachRoles($roles);
	}

	public function detachRoles($user_id, $roles)
	{
		return $this->model->find($user_id)->detachRoles($roles);
	}

	public function addRole($user_id, $role_id)
	{
		return $this->model->find($user_id)->attachRole($role_id);
	}

	public function detachRole($user_id, $role_id)
	{
		return $this->model->find($user_id)->detachRole($role_id);
	}

	protected function filters($query, $where)
	{
		if(array_key_exists('from', $where) && $where['from'] != null) {
			$from = new DateTime($where['from']);
			$query = $query->whereDate('created_at', '>=', $from);
		}

		if(array_key_exists('to', $where) && $where['to'] != null) {
			$from = new DateTime($where['to']);
			$query = $query->whereDate('created_at', '<=', $from);
		}

		if(array_key_exists('first_name', $where) && $where['first_name'] != null) {
			$query = $query->where('first_name', 'like', $where['first_name'] . '%');
		}

		if(array_key_exists('last_name', $where) && $where['last_name'] != null) {
			$query = $query->where('last_name', 'like', $where['last_name'] . '%');
		}

		if(array_key_exists('email', $where) && $where['email'] != null) {
			$query = $query->where('email', 'like', $where['email'] . '%');
		}

		if(array_key_exists('username', $where) && $where['username'] != null) {
			$query = $query->where('username', 'like', $where['username'] . '%');
		}

		if(array_key_exists('fullname', $where) && $where['fullname'] != null) {
			$name = explode(' ', $where['fullname']);
			$query = $query->where('first_name', 'like', $name[0] . '%')
				->where('last_name', 'like', $name[1]  . '%');
		}

		if(array_key_exists('user_id', $where) && $where['user_id'] != null) {
			$query = $query->where('user_id', '!=', $where['user_id'] . '%');
		}

		return $query;
	}

}