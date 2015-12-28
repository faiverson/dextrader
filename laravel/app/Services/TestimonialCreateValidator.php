<?php

namespace App\Services;

class TestimonialCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'author' => 'required',
		'image' => 'required',
		'text' => 'required'
	);

}