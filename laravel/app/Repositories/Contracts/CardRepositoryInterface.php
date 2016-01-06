<?php namespace App\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
/**
 * The UserRepositoryInterface contains ONLY method signatures for methods
 * related to the User object.
 *
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface CardRepositoryInterface extends RepositoryInterface {
	public function isCard($number);
	public function findUserCard($user_id, $card_id);
}