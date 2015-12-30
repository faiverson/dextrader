<?php

namespace App\Services;

class SubscriptionCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exists:users,id',
		'enroller_id' => 'sometimes|exists:users,id',
		'product_id' => 'required|exists:products,id',
		'card_id' => 'required|exists:credit_cards,id',
		'billing_address_id' => 'required|exists:billing_address,id',
		'status' => ['regex:/^([active|cancel|auto_cancel|admin_cancel]+)+$/'],
		'attempts_billing' => 'sometimes|numeric',
		'amount' => 'required',
		'last_billing' => 'required|date_format:Y-m-d',
		'next_billing' => 'required|date_format:Y-m-d'
	);

}