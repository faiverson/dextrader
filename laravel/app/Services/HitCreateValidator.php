<?php

namespace App\Services;

class HitCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'funnel_id' => 'required|exists:funnels,id',
		'product_id' => 'required|exists:products,id',
		'ip_address' => 'ip',
		'enroller_id' => 'sometimes|exists:users,id'
	);

}