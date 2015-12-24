<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use User;

class CommissionUpdateValidator extends AbstractValidator {


	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'from_user_id' => 'required|exists:users,id',
		'to_user_id' => 'required|exists:users,id',
		'invoices_id' => 'required|exists:invoices,id',
		'amount' => 'required',
		'type' => 'sometimes|required'
	);


}