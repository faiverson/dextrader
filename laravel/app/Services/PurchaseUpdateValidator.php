<?php

namespace App\Services;
use Illuminate\Validation\Factory;
use Illuminate\Http\Request;
use User;

class PurchaseUpdateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exists:users,id',
		'transaction_id' => 'required|exists:transactions,id',
		'enroller_id' => 'sometimes|exists:users,id',
		'funnel_id' => 'sometimes|exists:funnels,id',
		'tag_id' => 'sometimes|exists:campaign_tags,id',
		'invoice_id' => 'required|exists:invoices,id',
		'subscription_id' => 'required|exists:subscriptions,id',
	);

}