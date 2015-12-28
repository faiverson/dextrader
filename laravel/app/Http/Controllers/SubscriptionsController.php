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

}
