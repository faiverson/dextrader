<?php
namespace App\Repositories;

use App\Repositories\Contracts\TrainingRepositoryInterface;
use App\Repositories\Abstracts\Repository as AbstractRepository;
use App\Models\Training;
use DB;
use DateTime;

class TrainingRepository extends AbstractRepository implements TrainingRepositoryInterface
{
	// This is where the "magic" comes from:
	public function model()
	{
		return Training::class;
	}

	public function getCertification($userId)
	{
		return DB::table('trainings as t')
			->select([
				't.id',
				't.video_id',
				't.title',
				't.description',
				't.time',
				't.unlock_at',
				DB::raw('IF(ut.user_id, 1, 0) as completed')
			])
			->leftJoin('users_trainings as ut', function ($join) use ($userId) {
				$join->on('ut.training_id', '=', 't.id')->on('ut.user_id', '=', DB::raw($userId));
			})
			->where('t.type', 'certification')
			->get();
	}

	public function getUserTraining($training_id, $userId)
	{
		return DB::table('users_trainings')
			->where('training_id', $training_id)
			->where('user_id', $userId)
			->count();
	}

	/**
	 *
	 * How many trainings a user has
	 *
	 * @param String $type the type of the training [affiliate|pro|certification]
	 * @return Integer
	 */
	public function getTrainingCompletedByUser($type, $userId)
	{
		return DB::table('users_trainings')
			->where('type', $type)
			->where('user_id', $userId)
			->count();
	}

	/**
	 *
	 * How many trainings by type
	 *
	 * @return Integer
	 */
	public function getTotalTrainings($type)
	{
		return $this->model->where('type', $type)->count();
	}

	public function addUserTraining($training_id, $userId, $type)
	{
		$now = new DateTime('now');
		return DB::table('users_trainings')->insert([
			'training_id' => $training_id,
			'user_id' => $userId,
			'type' => $type,
			'created_at' => $now->format('Y-m-d H:i:s')
		]);
	}

	public function findTotalByType($type)
	{
		return $this->model->where('type', $type)->count();
	}

}