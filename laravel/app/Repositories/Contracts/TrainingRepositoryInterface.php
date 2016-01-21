<?php namespace App\Repositories\Contracts;

use App\Repositories\Contracts\RepositoryInterface;
/**
 * The UserRepositoryInterface contains ONLY method signatures for methods
 * related to the User object.
 *
 * Note that we extend from RepositoryInterface, so any class that implements
 * this interface must also provide all the standard eloquent methods (find, all, etc.)
 */
interface TrainingRepositoryInterface extends RepositoryInterface {
	public function getCertification($userId);
	public function getUserTraining($training_id, $userId);
	public function getTrainingCompletedByUser($type, $userId);
	public function getTotalTrainings($type);
}