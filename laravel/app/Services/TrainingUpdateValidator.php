<?php

namespace App\Services;
use App\Models\Training;
use Illuminate\Validation\Factory;

class TrainingUpdateValidator extends AbstractValidator {

	protected $id;
	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'video_id' => 'sometimes|VideoId',
		'type' => ['sometimes', 'regex:/^([affiliates|certification|pro]+)+$/'],
		'title' => 'sometimes',
		'filename' => 'sometimes',
		'time' => 'sometimes'
	);

	protected $messages = array(
		'video_id.video_id' => 'The video ID belong is already loaded'
	);

	public function custom()
	{
		$this->factory->extend('VideoId', function($attribute, $value, $parameters, $validator) {
			$is = Training::where('video_id', $value)->where('id', '!=', $this->data['id'])->count();
			return $is > 0 ? false : true;
		});
	}
}