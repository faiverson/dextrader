<?php

namespace App\Services;

class CommissionTotalCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exist:users,id'
	);

}