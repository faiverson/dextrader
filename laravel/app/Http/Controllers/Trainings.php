<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Training;
use DB;
use Role;
use DateTime;
use Token;
use Files;

class Trainings extends Controller
{

	protected $user;
	protected $userId;

	public function __construct()
	{
		$this->user = Auth::user();
		$this->userId = $this->user->id;
	}

	/**
	 *
	 * List of all the training affiliate type
	 *
	 * @param Request $request
	 * @return mixed json object
	 */
    public function affiliates()
    {
        $trainings = Training::where('type', 'affiliates')->get();
		return response()->ok($trainings);
    }

	/**
	 *
	 * List of all the training certification type
	 *
	 * @param Request $request
	 * @return mixed json object
	 */
	public function certification(Request $request)
	{
		$userId = $this->userId;
		$fields = [
			't.id',
			't.video_id',
			't.title',
			't.description',
			't.time',
			't.unlock_at',
			DB::raw('IF(ut.user_id, 1, 0) as completed')
		];
		$trainings = DB::table('trainings as t')
			->select($fields)
			->leftJoin('users_trainings as ut', function ($join) use ($userId) {
				$join->on('ut.training_id', '=', 't.id')->on('ut.user_id', '=', DB::raw($userId));
			})
			->where('t.type', 'certification')
			->get();
		return response()->ok($trainings);
	}

	/**
	 *
	 * List of all the training pro type
	 *
	 * @param Request $request
	 * @return mixed json object
	 */
	public function pro()
	{
		$trainings = Training::where('type', 'pro')->get();
		return response()->ok($trainings);
	}

	/**
	 *
	 * Save a checkpoint when the user has watched a training video
	 *
	 * @param Request $request
	 * @return mixed (token if all the traning has been watched)
	 */
	public function checkpoint(Request $request)
	{
		$training_id = $request->input('training_id');

		// since there isn't support in Eloquent to
		// composite primary keys, I decided to use
		// query builder instead
		$now = new DateTime('now');
		$isOne = $this->getUserTraining($training_id);
		if($isOne <= 0) {
			DB::table('users_trainings')->insert([
				'training_id' => $training_id,
				'user_id' => $this->userId,
				'type' => 'certification',
				'created_at' => $now->format('Y-m-d H:i:s')
			]);
		} else {
			return response()->error('The training is already completed');
		}

		// check if the trainings are completed
		$total_training = $this->getTotalTrainings('certification');
		// we want to update the token with a new permission
		$total_completed = $this->getTrainingCompletedByUser('certification');

		if($total_training == $total_completed) {
			$role = Role::where('name', 'certification.training')->first();
			$this->user->attachRole($role->id);
			$token = Token::refresh($request);
			return response()->ok(array('token' => $token));
		}

		return response()->added();
	}

	public function download(Request $request)
	{
		$training_id = $request->training_id;
		$doc = Training::find($training_id);
		if($doc->type === 'certification') {
			$unblock = $this->getUserTraining($training_id);
			if($unblock <= 0) {
				return response()->error('The user does not unblock the video.');
//				return view('errors.404', array('error' => 'The user does not unblock the video.'));
			}
		}
		return Files::download('trainings/' . $doc->filename, true);
	}

	/**
	 *
	 * Check if the user has a specific training
	 *
	 * @param Integer $training_id
	 * @return Integer
	 */
	protected function getUserTraining($training_id)
	{
		return DB::table('users_trainings')
			->where('training_id', $training_id)
			->where('user_id', $this->userId)
			->count();
	}

	/**
	 *
	 * How many trainings a user has
	 *
	 * @param String $type the type of the training [affiliate|pro|certification]
	 * @return Integer
	 */
	protected function getTrainingCompletedByUser($type)
	{
		return DB::table('users_trainings')
			->where('type', $type)
			->where('user_id', $this->userId)
			->count();
	}

	/**
	 *
	 * How many trainings by type
	 *
	 * @return Integer
	 */
	protected function getTotalTrainings($type)
	{
		return Training::where('type', $type)->count();
	}
}
