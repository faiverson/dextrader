<?php

namespace App\Http\Controllers;

use App\Gateways\SubscriptionGateway;
use Illuminate\Http\Request;
use Config;

class SubscriptionsController extends Controller
{
	public function __construct(SubscriptionGateway $gateway)
	{
		$this->gateway = $gateway;
		$this->limit = Config::get('dextrader.limit');
	}
	/**
	 * Show array of user cards
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$user_id = $request->id;
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$response = $this->gateway->findByUser($user_id, null, $limit, $offset);
		return response()->ok($response);
	}

	public function update(Request $request)
	{
		$user_id = $request->id;
		$subscription_id = $request->subscription_id;
		$isOwner = $this->gateway->isOwner($user_id, $subscription_id);
		if($isOwner > 0) {
			// we want only update these fields
			$response = $this->gateway->update([
				'card_id' => $request->input('card_id'),
				'billing_address_id' => $request->input('billing_address_id')
			], $subscription_id);
			if(!$response) {
				return response()->error($this->gateway->errors());
			}
		} else {
			return response()->error('You do not have permissions to set this subscription!');
		}

		return response()->ok();
	}

}
