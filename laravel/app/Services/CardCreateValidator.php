<?php

namespace App\Services;

use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use Cards;

class CardCreateValidator extends AbstractValidator {

	protected $id;
	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exists:users,id',
		'name' => 'required',
		'exp_month' => ['regex:/^(0?[1-9]|1[012])$/'],
		'exp_year' => ['regex:/^[0-9]{2}$/'],
		'number' => ['regex:/^[0-9]{14,16}$/'],
		'last_four' => ['regex:/^[0-9]{4}$/'],
		'first_six' => ['regex:/^[0-9]{6}$/']
	);

	public function __construct(Factory $validator, Request $request)
	{
		parent::__construct($validator);
//		$this->custom();
	}

//	public function custom()
//	{
//		$this->validator->extendImplicit('validNumber', function($attribute, $value, $parameters, $validator) {
//			$type = !empty($parameters['type']) ? $parameters['type'] : null;
//			$card = Cards::validCreditCard($value, $type);
//		});
//	}
}