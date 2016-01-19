<?php namespace App\Services;

use App\Models\IBSignal;
use App\Models\FXSignal;
use App\Models\NASignal;
use Illuminate\Validation\Factory;

class LiveSignalCreateValidator extends AbstractValidator {
	/**
	 * Validation for creating a new live signal
	 *
	 * @var array
	 */
	protected $rules = array(
		'signal_time' => ['required', 'date_format: Y-m-d H:i:s'],
		'expiry_time' => ['sometimes', 'date_format: Y-m-d H:i:s'],
		'asset' => ['required', 'regex:/^[a-zA-Z]{3}\/{0,1}[a-zA-Z]{3}$/'],
		'trade_type' => ['required', 'regex:/^([M1|M5|M15|M30|H1|H4|D1|W1|MN1]+)+$/'],
		'target_price'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/'],
		'end_price'=> ['sometimes', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'close_time' => ['sometimes', 'date_format: Y-m-d H:i:s'],
		'close_price'=> ['sometimes','regex:/[0-9]+[.,]?[0-9]*/']
	);

	public function __construct(Factory $validator)
	{
		parent::__construct($validator);
	}

	public function after($validator)
	{
		$data = $validator->getData();
		if(array_key_exists('mt_id', $data) &&
			array_key_exists('trade_type', $data) &&
			array_key_exists('type_product', $data)) {
			if($data['type_product'] == 'ib') {
				$many = IBSignal::where('mt_id', $data['mt_id'])->where('trade_type', $data['trade_type'])->count();
			}
			elseif($data['type_product'] == 'na') {
				$many = NASignal::where('mt_id', $data['mt_id'])->where('trade_type', $data['trade_type'])->count();
			}
			elseif($data['type_product'] == 'fx') {
				$many = FXSignal::where('mt_id', $data['mt_id'])->where('trade_type', $data['trade_type'])->count();
			}

			if($many > 0) {
				$validator->errors()->add('trade_type', 'There is a current signal with that ID and trade type');
			}
		}
	}
}