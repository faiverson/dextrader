<?php

namespace App\Services;

class BillingAddressCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exists:users,id',
		'address' => 'required',
		'city' => 'required',
		'state' => 'required',
		'country' => 'required',
		'zip' => 'required',
		'phone' => 'sometimes|required|between:8,30',
	);

}