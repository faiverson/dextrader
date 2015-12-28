<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use Cards;

class CardUpdateValidator extends AbstractValidator {

	protected $id;
	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'name' => 'required',
		'month' => ['regex:/^(0[1-9]|1[012])$/'],
		'year' => ['regex:/^[0-9]{2}$/'],
	);

	public function __construct(Factory $validator, Request $request)
	{
		parent::__construct($validator);
	}
}