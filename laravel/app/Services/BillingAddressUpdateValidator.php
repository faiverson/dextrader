<?php

namespace App\Services;

class BillingAddressUpdateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'address' => 'sometimes|required',
		'city' => 'sometimes|required',
		'state' => 'sometimes|required',
		'country' => 'sometimes|required',
		'zip' => 'sometimes|required',
		'phone' => 'sometimes|required|between:8,30',
	);

}