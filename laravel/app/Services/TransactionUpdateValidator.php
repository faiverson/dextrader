<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use Transaction;

class TransactionUpdateValidator extends AbstractValidator {

	protected $messages = array(
		'enroller_id.exists' => 'The sponsor is not in database',
		'tag_id.exists' => 'The tag is not in database',
		'funnel_id.exists' => 'The funnel is not in database'
	);

	/**
	 * Validation for updating a Transaction
	 *
	 * @var array
	 */
	protected $rules = array(
		'first_name' => 'sometimes|required',
		'last_name' => 'sometimes|required',
		'email' => 'sometimes|required|email|unique:users|max:150',

		'enroller_id' => 'sometimes|exists:users,id',
		'funnel_id'=> 'sometimes|exists:funnels,id',
		'tag_id' => 'sometimes|exists:campaign_tags,id',
		'ip_address' => 'sometimes|ip',

		'product_id' => 'sometimes|exists:products,id',
		'product_name' => 'sometimes|required|unique:products|alpha',
		'product_amount' => ['sometimes', 'required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'product_discount' => ['sometimes', 'required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'amount' => ['sometimes', 'required', 'regex:/[0-9]+[.,]?[0-9]*/'],

		'billing_address_id' => 'sometimes|numeric',
		'billing_address' => 'sometimes|required',
		'billing_city' => 'sometimes|required',
		'billing_state' => 'sometimes|required',
		'billing_country' => 'sometimes|required',
		'billing_zip' => 'sometimes|required',
		'billing_phone' => 'sometimes|required|digits_between:8,30',

		'card_id' => 'sometimes|numeric',
		'card_name' => 'sometimes|required',
		'card_exp_month' => ['sometimes','required','regex:/^(0[1-9]|1[012])$/'],
		'card_exp_year' => ['sometimes','required','regex:/^[0-9]{2}$/'],
		'number' => ['sometimes','required','regex:/^[0-9]{14,16}$/'],
		'card_network' => 'sometimes|required',
		'card_first_six' => ['sometimes', 'required', 'regex:/^[0-9]{6}$/'],
		'card_last_four'=> ['sometimes', 'required', 'regex:/^[0-9]{4}$/'],
		'cvv' => ['sometimes', 'required', 'regex:/^[0-9]{3,4}$/']
	);
}