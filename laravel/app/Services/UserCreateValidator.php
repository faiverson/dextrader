<?php

namespace App\Services;

class UserCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'first_name' => 'required',
		'last_name' => 'required',
		'email' => 'required|email|unique:users|max:150',
		'username' => 'required|unique:users|min:4|max:50',
		'phone' => 'sometimes|required|numeric|min:8|max:20',
		'password' => 'required|min:8'
	);

}