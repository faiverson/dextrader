<?php

namespace App\Services;

class CommissionTotalUpdateValidator extends AbstractValidator {


	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exists:users,id'
	);


}