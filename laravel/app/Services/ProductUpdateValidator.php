<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use User;

class ProductUpdateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'name' => 'sometimes|alpha',
		'display_name' => 'sometimes|alpha',
		'amount' => ['sometimes', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'discount' => ['sometimes', 'regex:/[0-9]+[.,]?[0-9]*/'],
	);

}