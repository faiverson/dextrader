<?php

namespace App\Services;

class TagCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exists:users,id',
		'tag' => 'required|unique:campaign_tags',
	);

}