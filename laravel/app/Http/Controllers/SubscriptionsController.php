<?php

namespace App\Http\Controllers;

use App\Events\SubscriptionCancelEvent;
use App\Gateways\SubscriptionGateway;
use Illuminate\Http\Request;
use Config;
use Event;

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

	public function edit(Request $request)
	{
		$user_id = $request->id;
		$subscription_id = $request->subscription_id;
		$response =  $this->update($user_id, $subscription_id, [
			'card_id' => $request->input('card_id'),
			'billing_address_id' => $request->input('billing_address_id')
		]);

		if (!$response) {
			return response()->error($response);
		}
		return response()->ok();
	}

	public function cancel(Request $request)
	{
		$user_id = $request->id;
		$subscription_id = $request->subscription_id;
		$response = $this->update($user_id, $subscription_id, [
			'status' => 'cancel'
		]);

		if (!$response) {
			return response()->error($response);
		}

		$sub = $this->gateway->find($subscription_id);
		Event::fire(new SubscriptionCancelEvent($sub));
		return response()->ok();
	}


	public function reactive(Request $request)
	{
		$user_id = $request->id;
		$subscription_id = $request->subscription_id;
		$sub = $this->gateway->find($subscription_id);
		$there_subs = $this->gateway->findSubscriptionsByUserProduct($user_id, $sub->product_id);
		if($there_subs > 0) {
			return response()->error('You cannot active this subscription because you have another activated!');
		}

		$response = $this->update($user_id, $subscription_id, [
			'status' => 'active'
		]);

		if(!$response) {
			return response()->error($response);
		}

		$sub = $this->gateway->find($subscription_id);
		Event::fire(new SubscriptionCancelEvent($sub));
		return response()->ok();
	}

	public function update($user_id, $subscription_id, $data)
	{
		// to make sure this subscription belong to the user
		$isOwner = $this->gateway->isOwner($user_id, $subscription_id);
		if($isOwner > 0) {
			$response = $this->gateway->update($data, $subscription_id);
			if(!$response) {
				return $this->gateway->errors();
			}
		} else {
			return ['You do not have permissions to set this subscription!'];
		}

		return $response;
	}

}
