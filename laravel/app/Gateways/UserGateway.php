<?php
namespace App\Gateways;

use App\Gateways\AbstractGateway;
use App\Services\UserCreateValidator;
use App\Services\UserUpdateValidator;
use App\Repositories\UserRepository;

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

    public function add(array $data)
    {
        if (array_key_exists('enroller', $data)) {
            $enroller = $this->findBy('username', $data['enroller'], ['id'])->first();

            if($enroller){
                $data['enroller_id'] = $enroller->id;
                unset($data['enroller']);
            }
        }

        return $this->create($data);
    }

    public function edit(array $data, $id)
    {
        if (array_key_exists('username', $data)) {
            unset($data['username']);
        }

        if (array_key_exists('full_name', $data)) {
            unset($data['full_name']);
        }

        return $this->update($data, $id);
    }

    public function getIdByUsername($username)
    {
        return $this->repository->getIdByUsername($username);
    }

    public function attachRole($user_id, $role_id)
    {
        return $this->repository->addRole($user_id, $role_id);
    }

    public function actives($columns = array('*'), $limit = null, $offset = null, $order_by = null)
    {
        return $this->repository->actives($columns, $limit, $offset, $order_by);
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
        return $this->repository->findById($id, $column = 'id', $columns = array('*'));
    }

}