<?php namespace App\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
/**
 * The UserRepositoryInterface contains ONLY method signatures for methods
 * related to the User object.
 *
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface UserRepositoryInterface extends RepositoryInterface {
	public function actives($columns = array('*'), $limit = null, $offset = null, $order_by = null);
	public function findById($id, $column = 'id', $columns = array('*'));
	public function findByUsername($username, $columns = array('*'));
	public function getIdByUsername($username);
	public function addRole($user_id, $role_id);
}