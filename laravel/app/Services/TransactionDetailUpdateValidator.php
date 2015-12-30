<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use Transaction;

class TransactionDetailUpdateValidator extends AbstractValidator {

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
		'transaction_id' => 'sometimes|exists:transactions,id',
		'product_id' => 'sometimes|exists:products,id',
		'product_name' => 'sometimes|required|unique:products|alpha',
		'product_amount' => ['sometimes', 'required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'product_discount' => ['sometimes', 'required', 'regex:/[0-9]+[.,]?[0-9]*/']
	);
}