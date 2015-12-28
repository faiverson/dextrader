<?php

namespace App\Services;

class CommissionCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'from_user_id' => 'required|exist:users,id',
		'to_user_id' => 'required|exist:users,id',
		'invoices_id' => 'required|exist:invoices,id',
		'amount' => 'required'
	);

}