<?php
namespace App\Gateways;

use App\Exceptions\UserException;
use App\Gateways\AbstractGateway;
use App\Services\UserCreateValidator;
use App\Services\UserUpdateValidator;
use App\Repositories\UserRepository;
use DB;
use Log;

class UserGateway extends AbstractGateway
{

    protected $repository;

    protected $createValidator;

    protected $updateValidator;

    protected $role;

    protected $errors;

    public function __construct(UserRepository $repository, UserCreateValidator $createValidator, UserUpdateValidator $updateValidator, RoleGateway $role)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;
        $this->role = $role;
    }

	public function signup(array $data)
	{
		$data['roles'] = [$this->role->getRoleIdByName('user')];
		return $this->add($data);
	}

    public function add(array $data)
    {
        if (array_key_exists('enroller', $data)) {
            $enroller = $this->findBy('username', $data['enroller'], ['id'])->first();

            if($enroller){
                $data['enroller_id'] = $enroller->id;
                unset($data['enroller']);
            }
        }

		DB::beginTransaction();
		try {
			$user = $this->create($data);
			if(!$user) {
				throw new UserException($this->errors());
			}
			if (array_key_exists('roles', $data) && (
					(is_array($data['roles']) && count($data['roles']) > 0) ||
					is_string($data['roles']) && strlen($data['roles']) > 0)
			) {
				$user = $this->repository->find($user->id);
				$roles = array_column($user->roles->toArray(), 'role_id');
				$this->repository->detachRoles($user->id, $roles);
				$new_roles = is_array($data['roles']) ? $data['roles'] : json_decode($data['roles']);
				$this->repository->addRoles($user->id, $new_roles);
			}
		}
		catch(\Exception $e) {
			DB::rollback();
			$type = '';
			$this->errors = json_decode($e->getMessage());
			Log::info((array)$this->errors);
			Log::info($e->getMessage());
			if ($e instanceof UserException) {
				$type .= 'UserException';
			}
			Log::error($type . ': ', ['error' => (array)$this->errors, 'data' => $data]);
			return false;
		}
		DB::commit();
		return $user;
    }

    public function edit(array $data, $id, $user)
    {
		if(!$user->can('user.update')) {
			if (array_key_exists('username', $data)) {
				unset($data['username']);
			}

			if (array_key_exists('enroller_id', $data)) {
				unset($data['enroller_id']);
			}
		}

        if (array_key_exists('full_name', $data)) {
            unset($data['full_name']);
        }

		$response = $this->update($data, $id);
		if($response) {
			if (array_key_exists('roles', $data) && (
				(is_array($data['roles']) && count($data['roles']) > 0) ||
				is_string($data['roles']) && strlen($data['roles']) > 0)
			) {
				$user = $this->repository->find($id);
				$roles = array_column($user->roles->toArray(), 'role_id');
				$this->repository->detachRoles($id, $roles);
				$new_roles = is_array($data['roles']) ? $data['roles'] : json_decode($data['roles']);
				$this->repository->addRoles($id, $new_roles);
			}
		}

        return $response;
    }

    public function getIdByUsername($username)
    {
        return $this->repository->getIdByUsername($username);
    }

    public function attachRole($user_id, $role_id)
    {
        return $this->repository->addRole($user_id, $role_id);
    }

	public function deatachRole($user_id, $role_id)
	{
		return $this->repository->detachRole($user_id, $role_id);
	}

    public function actives($columns = array('*'), $limit = null, $offset = null, $order_by = null, $filters = null)
    {
        return $this->repository->actives($columns, $limit, $offset, $order_by, $filters);
    }

	public function totalActives($filters = null)
	{
		return $this->repository->total($filters);
	}

	public function getUserBySponsor($enroller_id, $columns = array('*'), $limit = null, $offset = null, $order_by = null, $filters = null)
	{
		return $this->repository->getUserBySponsor($enroller_id, $columns, $limit, $offset, $order_by, $filters);
	}

	public function getTotalUserBySponsor($enroller_id, $filters = null)
	{
		return $this->repository->getTotalUserBySponsor($enroller_id, $filters);
	}

	public function revoke($user_id, $role_id)
	{
		return $this->repository->detachRole($user_id, $role_id);
	}

	public function getRoleByName($name)
	{
		return $this->role->getRoleIdByName($name);
	}

    public function findById($id, $column = 'id', $columns = array('*'))
    {
        return $this->repository->findById($id, $column, $columns);
    }

}