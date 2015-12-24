<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use User;

class UserUpdateValidator extends AbstractValidator {

	protected $id;
	protected $email;
	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'first_name' => 'sometimes|required',
		'last_name' => 'sometimes|required',
		'email' => 'sometimes|required|email|MyEmail',
		'phone' => 'sometimes|required|digits_between:8,30',
		'password' => ['sometimes', 'regex:/^(?=.{8,30}$)(?![_.])(?!.*[_.]{2})[a-zA-Z0-9._]+(?<![_.])$/'],
		'ip_address' => 'sometimes|ip',
	);

	protected $messages = array(
		'email.my_email' => 'The email selected belong to another user in the system'
	);

	public function __construct(Factory $validator, Request $request)
	{
		parent::__construct($validator);
		$user = $request->user();
		if($user) {
			$this->id = $user->id;
			$this->email = $request->user()->email;
		}
	}

	public function custom()
	{
		$this->validator->extendImplicit('MyEmail', function($attribute, $value, $parameters, $validator) {
			// if it is different from the current email, we check if someone else have it
			if($value != $this->email) {
				$is = User::where('email', $value)->where('id', $this->id)->count();
				return $is > 0 ? false : true;
			}
			return true;
		});
	}

}