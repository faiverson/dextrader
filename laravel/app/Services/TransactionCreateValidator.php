<?php namespace App\Services;

use Illuminate\Validation\Factory;
use Transaction;
class TransactionCreateValidator extends AbstractValidator {

	protected $messages = array(
		'enroller_id.exists' => 'The sponsor is not in database',
		'tag_id.exists' => 'The tag is not in database',
		'funnel_id.exists' => 'The funnel is not in database'
	);

	/**
	 * Validation for creating a new Transaction
	 *
	 * @var array
	 */
	protected $rules = array(
		'first_name' => 'required',
		'last_name' => 'required',
		'email' => 'required|email|max:150',

		'user_id' => 'required|exists:users,id',
		'enroller_id' => 'sometimes|required|exists:users,id',
		'funnel_id'=> 'required|exists:funnels,id',
		'tag_id' => 'sometimes|required|exists:campaign_tags,id',
		'ip_address' => 'sometimes|ip',

		'product_id' => 'sometimes|required:exists:products,id',
		'product_name' => 'required|alpha',
		'product_amount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'product_discount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'amount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],

		'billing_address_id' => 'numeric',
		'billing_address' => 'required',
		'billing_city' => 'required',
		'billing_state' => 'required',
		'billing_country' => 'required',
		'billing_zip' => 'required',
		'billing_phone' => 'sometimes|required|digits_between:8,30',

		'card_id' => 'sometimes|numeric',
		'card_name' => 'required',
		'card_exp_month' => ['required','regex:/^(0?[1-9]|1[012])$/'],
		'card_exp_year' => ['required','regex:/^[0-9]{2}$/'],
		'number' => ['required','regex:/^[0-9]{14,16}$/'],
		'card_network' => 'required',
		'card_first_six' => ['required', 'regex:/^[0-9]{6}$/'],
		'card_last_four'=> ['required', 'regex:/^[0-9]{4}$/'],
		'cvv' => ['sometimes', 'required', 'regex:/^[0-9]{3,4}$/']
	);

	public function __construct(Factory $validator)
	{
		parent::__construct($validator);
	}

	public function after($validator)
	{
		$data = $validator->getData();
		if(Transaction::where('user_id', $data['user_id'])->where('product_id', $data['product_id'])->count() > 0) {
			$validator->errors()->add('user_id', 'You have bought this product already!');
		}
	}


}