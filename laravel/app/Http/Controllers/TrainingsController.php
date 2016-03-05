<?php

namespace App\Http\Controllers;

use App\Gateways\TrainingGateway;
use Illuminate\Http\Request;
use App\Models\Training;
use Role;
use Token;

class TrainingsController extends Controller
{
	public function __construct(TrainingGateway $gateway)
	{
		$this->gateway = $gateway;
	}

	public function show(Request $request){
		$training_id = $request->training_id;
		$training = $this->gateway->find($training_id);


		return response()->ok($training);
	}

	public function list_orders(Request $request){
		$certification = $this->gateway->findTotalByType('certification');
		$pro = $this->gateway->findTotalByType('pro');
		$affiliates = $this->gateway->findTotalByType('affiliates');

		return response()->ok([
			'Affiliates' => $affiliates,
			'Certification' => $certification,
			'Pro' => $pro
		]);
	}

	/**
	 *
	 * Store new trainings
	 *
	 * @param Request $request
	 * @return mixed json object
	 */
	public function store(Request $request)
	{
		$data = $request->all();
		if(array_key_exists('video_id', $data)) {
			$data['video_id'] = $this->gateway->parse_youtube($data['video_id']);
		}

		$response = $this->gateway->add($data);
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}

	public function update(Request $request)
	{
		$id = $request->training_id;
		$data = $request->all();
		if(array_key_exists('video_id', $data)) {
			$data['video_id'] = $this->gateway->parse_youtube($data['video_id']);
		}

		$response = $this->gateway->update($data, $id);
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
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
		$response = $this->gateway->findBy('type', 'affiliates');
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
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
		$userId = $request->user()->id;
		$response = $this->gateway->getCertification($userId);
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
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
		$response = $this->gateway->findBy('type', 'pro');
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}

	/**
	 *
	 * Save a checkpoint when the user has watched a training video
	 *
	 * @param Request $request
	 * @return mixed (token if all the training has been watched)
	 */
	public function checkpoint_certification(Request $request)
	{
		$userId = $userId = $request->user()->id;
		$training_id = $request->input('training_id');
		$response = $this->gateway->checkpoint($training_id, $userId, 'certification');
		if(!$response) {
			return response()->error($this->gateway->errors());
		}

		// check if the trainings are completed
		$total_training = $this->gateway->getTotalTrainings('certification');
		// we want to update the token with a new permission
		$total_completed = $this->gateway->getTrainingCompletedByUser('certification', $userId);

		if($total_training == $total_completed) {
			$role = Role::where('name', 'certification.training')->first();
			$request->user()->attachRole($role->id);
			$token = Token::refresh($request);
			return response()->ok(array('token' => $token));
		}

		return response()->added();
	}

	public function destroy(Request $request)
	{
		$training_id = $request->training_id;
		$response = $this->gateway->destroy($training_id);
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 * @TODO remove this permanently?!
	 */
	public function download(Request $request)
	{
		$training_id = $request->training_id;
		$doc = Training::find($training_id);
		if($doc->type === 'certification') {
////			$unblock = $this->getUserTraining($training_id);
//			if($unblock <= 0) {
//				return response()->error('The user does not unblock the video.');
////				return view('errors.404', array('error' => 'The user does not unblock the video.'));
//			}
		}
		return Files::download('trainings/' . $doc->filename, true, $doc->filename);
	}
}
