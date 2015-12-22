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
		// http://stackoverflow.com/questions/12018245/regular-expression-to-validate-username
		'username' => ['required', 'unique:users', 'regex:/^(?=.{4,30}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/'],
		'phone' => 'sometimes|required|digits_between:8,30',
		'password' => ['required', 'regex:/^(?=.{8,30}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/'],
		'ip_address' => 'sometimes|ip',
		'enroller_id' => 'sometimes|exists:users,id'
	);

	protected $messages = array(
		'enroller_id.exists' => 'The sponsor is not in database'
	);
}