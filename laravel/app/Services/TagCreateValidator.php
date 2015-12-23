<?php

namespace App\Services;

class TagCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'tag' => 'required|unique:campaign_tags',
	);

}