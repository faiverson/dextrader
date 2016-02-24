<?php namespace App\Services;

use Illuminate\Validation\Factory;
use App\Models\User;

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
		'password' => ['sometimes'],
		'ip_address' => 'sometimes|ip',
	);

	protected $messages = array(
		'email.my_email' => 'The email selected belong to another user in the system'
	);

	public function custom()
	{
		$this->factory->extend('MyEmail', function($attribute, $value, $parameters, $validator) {
			$is = User::where('email', $value)->where('id', '!=', $this->data['id'])->count();
			return $is > 0 ? false : true;
		});
	}

}