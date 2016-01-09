<?php

namespace App\Services;

class SpecialOfferCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'product_id' => 'required|exists:products,id',
		'amount' => 'required',
		'ending_dt' => 'required|date_format:Y-m-d',
	);

}