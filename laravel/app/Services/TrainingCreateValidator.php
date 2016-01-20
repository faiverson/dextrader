<?php

namespace App\Services;

class TrainingCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'video_id' => 'required|unique:trainings,video_id',
		'type' => ['required', 'regex:/^([affiliates|certification|pro]+)+$/'],
		'title' => 'required',
		'filename' => 'sometimes',
		'time' => 'required'
	);

}