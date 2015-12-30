<?php namespace App\Services;

use App\Models\Subscription;
use Illuminate\Validation\Factory;

class TransactionDetailCreateValidator extends AbstractValidator {
	/**
	 * Validation for creating a new Transaction
	 *
	 * @var array
	 */
	protected $rules = array(
		'transaction_id' => 'required|exists:transactions,id',
		'product_id' => 'required|exists:products,id',
		'product_name' => 'required|alpha',
		'product_amount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/'],
		'product_discount' => ['required', 'regex:/[0-9]+[.,]?[0-9]*/']
	);

	public function __construct(Factory $validator)
	{
		parent::__construct($validator);
	}

	public function after($validator)
	{
		$data = $validator->getData();
		if(array_key_exists('user_id', $data) && array_key_exists('product_id', $data)) {
			$many = Subscription::where('user_id', $data['user_id'])
				->where('product_id', $data['product_id'])
				->where('status', 'active')
				->count();
			if ($many > 0) {
				$validator->errors()->add('user_id', 'You have bought this product already!');
			}
		}
	}


}