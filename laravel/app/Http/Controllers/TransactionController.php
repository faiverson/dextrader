<?php

namespace App\Http\Controllers;

use App\Gateways\TransactionGateway;
use Illuminate\Http\Request;
use Config;
use Event;
use App\Events\CheckoutEvent;

class TransactionController extends Controller
{
	public function __construct(TransactionGateway $gateway)
	{
		$this->transaction = $gateway;
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
		$id = $request->id;
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$response = $this->transaction->findBy('user_id', $id, null, $limit, $offset);
		return response()->ok($response);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkout(Request $request)
	{
		$fields = $fields =$request->all();
		$fields['ip_address'] = $request->ip();
		// to simplify UI
		$fields['products'] = is_string($fields['products']) ? explode(',', $fields['products']) : $fields['products'];

		$response = $this->transaction->add($fields);
		if(!$response['transaction']) {
			return response()->error($this->transaction->errors());
		}

		// if everything is ok, we set the user and create the invoice
		if(strtolower($response['transaction']->responsetext) == 'success') {
			$purchase = $this->transaction->purchase($response);
			if(!$purchase) {
				return response()->error($this->transaction->errors());
			}
			$response = array_merge($response, $purchase);
			Event::fire(new CheckoutEvent($response));
		} else {
			return response()->error('There is a problem with the Merchant. Please contact support to solve the problem!');
		}

		return response()->ok($response['transaction']);
	}

}
