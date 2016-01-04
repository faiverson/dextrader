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

    protected $errors;

    public function __construct(UserRepository $repository, UserCreateValidator $createValidator, UserUpdateValidator $updateValidator)
    {
        $this->repository = $repository;
        $this->createValidator = $createValidator;
        $this->updateValidator = $updateValidator;
    }

    public function edit(array $data, $id)
    {
        if (array_key_exists('username', $data)) {
            unset($data['username']);
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

    public function findById($id, $column = 'id', $columns = array('*'))
    {
        return $this->repository->findById($id, $column = 'id', $columns = array('*'));
    }
}