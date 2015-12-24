<?php

namespace App\Services;

class PurchaseCreateValidator extends AbstractValidator {

	/**
	 * Validation for creating a new User
	 *
	 * @var array
	 */
	protected $rules = array(
		'user_id' => 'required|exists:users,id',
		'transaction_id' => 'required|exists:transactions,id',
		'enroller_id' => 'required|exists:users,id',
		'funnel_id' => 'required|exists:funnels,id',
		'tag_id' => 'required|exists:campaign_tags,id',
		'invoice_id' => 'required|exists:invoices,id',
		'subscription_id' => 'required|exists:subscriptions,id',
	);

}