<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;

class TestimonialUpdateValidator extends AbstractValidator {

	protected $id;
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