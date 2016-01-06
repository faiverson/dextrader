<?php

namespace App\Http\Controllers;

use App\Gateways\TransactionGateway;
use Illuminate\Http\Request;
use Config;
use Event;
use App\Events\CheckoutEvent;
use Log;

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
		if(!$response) {
			return response()->error($this->transaction->errors());
		}

		// if everything is ok, we set the user and create the invoice
		if(array_key_exists('responsetext', $response) && strtolower($response['responsetext']) == 'success') {
			$purchase = $this->transaction->purchase($response);
			if(!$purchase) {
				return response()->error($this->transaction->errors());
			}
			$response = array_merge($response, $purchase);
			Event::fire(new CheckoutEvent($response));
		} else {
			Log::info('Merchant', $response);
			if(!array_key_exists('responsetext', $response)) {
				$response['responsetext'] = 'There is a problem with the Merchant. Please contact support to solve the problem!';
			}
			return response()->error($response['responsetext']);

		}

		return response()->ok($response);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function upgrade(Request $request)
	{
		$fields = $fields =$request->all();
		$fields['ip_address'] = $request->ip();
		$fields['user_id'] = $request->id;

		// to simplify UI
		$fields['products'] = is_string($fields['products']) ? explode(',', $fields['products']) : $fields['products'];
		$response = $this->transaction->upgrade($fields);
		if(!$response) {
			return response()->error($this->transaction->errors());
		}

		// if everything is ok, we set the user and create the invoice
		if(array_key_exists('responsetext', $response) && strtolower($response['responsetext']) == 'success') {
			$purchase = $this->transaction->purchase($response);
			if(!$purchase) {
				return response()->error($this->transaction->errors());
			}
			$response = array_merge($response, $purchase);
			Event::fire(new CheckoutEvent($response));
		} else {
			Log::info('Merchant', $response);
			if(!array_key_exists('responsetext', $response)) {
				$response['responsetext'] = 'There is a problem with the Merchant. Please contact support to solve the problem!';
			}
			return response()->error($response['responsetext']);

		}

		return response()->ok($response);
	}
}
