<?php namespace App\Services;

class LiveSignalUpdateValidator extends AbstractValidator {

	/**
	 * Validation for uptading a live signal
	 *
	 * @var array
	 */
	protected $rules = array(
		'signal_time' => ['sometimes', 'date_format: Y-m-d H:i:s'],
		'expiry_time' => ['sometimes', 'date_format: Y-m-d H:i:s'],
		'asset' => ['sometimes', 'regex:/^[a-zA-Z]{3}\/{0,1}[a-zA-Z]{3}$/'],
		'target_sleep'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/'],
		'target_to'=> ['sometimes', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'end_price'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/'],
		'close_time' => ['sometimes', 'date_format: Y-m-d H:i:s'],
		'close_price'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/']
	);

}