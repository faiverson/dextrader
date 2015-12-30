<?php

namespace App\Services;

class InvoiceDetailCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'invoice_id' => 'required|exists:invoices,id',
		'product_id' => 'sometimes|required:exists:products,id',
		'product_name' => 'required|alpha',
		'product_amount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'product_discount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'subscription_id' => 'sometimes|required|exists:subscriptions,id'
	);

}