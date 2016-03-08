<?php

namespace App\Services;

class InvoiceUpdateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exists:users,id',
		'first_name' => 'required',
		'last_name' => 'required',
		'email' => 'required|email|max:150',

		'enroller_id' => 'sometimes|exists:users,id',
		'amount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],

		'funnel_id'=> 'sometimes|exists:funnels,id',
		'tag_id' => 'sometimes|exists:campaign_tags,id',

		'billing_address_id' => 'numeric',
		'billing_address' => 'required',
		'billing_city' => 'required',
		'billing_state' => 'required',
		'billing_country' => 'required',
		'billing_zip' => 'required',
		'billing_phone' => 'sometimes|required|between:8,30',

		'card_id' => 'sometimes|numeric',
		'card_name' => 'required',
		'card_exp_month' => ['required','regex:/^(0?[1-9]|1[012])$/'],
		'card_exp_year' => ['required','regex:/^[0-9]{2}$/'],
		'card_network' => 'required',
		'card_first_six' => ['required', 'regex:/^[0-9]{6}$/'],
		'card_last_four'=> ['required', 'regex:/^[0-9]{4}$/'],

		'ip_address' => 'sometimes|ip',
	);

}