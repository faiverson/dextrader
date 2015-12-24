<?php
namespace App\Repositories;

use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Role;

class RoleRepository extends AbstractRepository implements RoleRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return Role::class;
	}

	public function getRoleIdByName($name) {
		$role = $this->findBy('name', $name)->first();
		return  $role != null ? $role->id : null;
	}

}