<?php

namespace App\Services;

class SubscriptionUpdateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'sometimes|required|exists:users,id',
		'enroller_id' => 'sometimes|required|exists:users,id',
		'product_id' => 'sometimes|required|exists:products,id',
		'card_id' => 'sometimes|required|exists:credit_cards,id',
		'billing_address_id' => 'sometimes|required|exists:billing_address,id',
		'status' => 'regex:/^[active|cancel|auto_cancel|admin_cancel]$/',
		'attempts_billing' => 'sometimes|numeric',
		'amount' => 'sometimes|required',
		'last_billing' => 'sometimes|required|date_format:Y-m-d',
		'next_billing' => 'sometimes|required|date_format:Y-m-d'
	);

}