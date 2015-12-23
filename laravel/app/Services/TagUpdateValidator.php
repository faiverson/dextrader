<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use User;

class TagUpdateValidator extends AbstractValidator {

	protected $id;
	protected $email;
	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
//		'ta' => 'sometimes|required',
	);

//	protected $messages = array(
//		'tag.check' => 'The email selected belong to another user in the system'
//	);
//
//	public function __construct(Factory $validator, Request $request)
//	{
//		parent::__construct($validator);
//		$user = $request->user();
//		if($user) {
//			$this->id = $user->id;
//			$this->email = $request->user()->email;
//			$this->custom();
//		}
//	}
//
//	public function custom()
//	{
//		$this->validator->extendImplicit('tag', function($attribute, $value, $parameters, $validator) {
//			// if it is different from the current email, we check if someone else have it
//			if($value != $this->email) {
//				$is = User::where('email', $value)->where('id', $this->id)->count();
//				return $is > 0 ? false : true;
//			}
//			return true;
//		});
//	}

}