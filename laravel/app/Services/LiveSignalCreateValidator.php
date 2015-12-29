<?php namespace App\Services;

use App\Models\LiveSignal;
use Illuminate\Validation\Factory;

class LiveSignalCreateValidator extends AbstractValidator {
	/**
	 * Validation for creating a new live signal
	 *
	 * @var array
	 */
	protected $rules = array(
		'signal_date' => 'required|date_format:Y-m-d',
		'signal_time' => ['required', 'regex:/^[0-9]{2}\:[0-9]{2}$/'],
		'expiry_time' => ['sometimes', 'regex:/^[0-9]{2}\:[0-9]{2}$/'],
		'asset' => ['required', 'regex:/^[a-zA-Z]{3}\/[a-zA-Z]{3}$/'],
		'asset_rate' => ['required', 'regex:/^[0-9]{2}$/'],
		'target_price'=> ['required','regex:/[0-9]+[.,]?[0-9]*/'],
		'end_price'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/']
	);

	public function __construct(Factory $validator)
	{
		parent::__construct($validator);
	}

	public function after($validator)
	{
		$data = $validator->getData();
		if(array_key_exists('signal_date', $data) && array_key_exists('asset', $data)) {
			$many = LiveSignal::where('signal_date', $data['signal_date'])
				->where('asset', $data['asset'])
				->count();
			if($many > 0) {
				$validator->errors()->add('signal_date', 'There is a current asset for that date.');
			}
		}

	}
}