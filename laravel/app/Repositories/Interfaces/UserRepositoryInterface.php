<?php namespace Acme\Repositories;

use Libraries\dtrader\RepositoryInterface;
/**
 * The UserRepositoryInterface contains ONLY method signatures for methods
 * related to the User object.
 *
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface UserRepositoryInterface extends RepositoryInterface {

	public function findUserByUsername($username);
}