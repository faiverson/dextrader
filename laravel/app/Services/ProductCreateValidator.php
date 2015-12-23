<?php

namespace App\Services;

class ProductCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'name' => 'required|unique:products|alpha',
		'display_name' => 'required|alpha',
		'amount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'discount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],
	);

}