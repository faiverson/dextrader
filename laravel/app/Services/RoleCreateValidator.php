<?php

namespace App\Services;

class RoleCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'name' => 'sometimes|required|alpha',
		'display_name' => 'sometimes|required|alpha'
	);

}