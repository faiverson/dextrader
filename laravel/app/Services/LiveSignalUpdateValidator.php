<?php namespace App\Services;

class LiveSignalUpdateValidator extends AbstractValidator {

	/**
	 * Validation for uptading a live signal
	 *
	 * @var array
	 */
	protected $rules = array(
//		'signal_date' => 'sometimes|date_format:Y-m-d',
		'signal_time' => ['sometimes', 'regex:/^[0-9]{2}\:[0-9]{2}$/'],
		'expiry_time' => ['sometimes', 'regex:/^[0-9]{2}\:[0-9]{2}$/'],
		'asset' => ['sometimes', 'regex:/^[a-zA-Z]{3}\/{0,1}[a-zA-Z]{3}$/'],
//		'asset_rate' => ['sometimes', 'regex:/^[0-9]{2}$/'],
		'target_price'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/'],
		'end_price'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/'],
		'close_price'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/']
	);

}