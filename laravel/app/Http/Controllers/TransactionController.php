<?php

namespace App\Http\Controllers;

use App\Events\CheckoutFailedEvent;
use App\Gateways\TransactionGateway;
use Illuminate\Http\Request;
use Config;
use Event;
use App\Events\CheckoutEvent;
use App\Events\RefundEvent;
use Log;
use Token;

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
		$response = $this->transaction->findBy('id', $id, null, $limit, $offset);
		return response()->ok($response);
	}

	public function by_user(Request $request)
	{
		$id = $request->id;
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$order_by = $request->input('order') ? $request->input('order') : ['id' => 'desc'];
		$filters = $request->input('filter') ? $request->input('filter') : [];
		$transactions = $this->transaction->showUserTransactions($id, $limit, $offset, $order_by, $filters);
		$total = $this->transaction->showTotalUserTransactions($id, $filters);
		return response()->ok([
			'transactions' => $transactions,
			'total' => $total
		]);
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
		if(array_key_exists('offer_id', $fields)) {
			$fields['offer_id'] = is_string($fields['offer_id']) ? explode(',', $fields['offer_id']) : $fields['offer_id'];
		}

		$response = $this->transaction->add($fields);
		if(!$response) {
			return response()->error($this->transaction->errors());
		}

		// if everything is ok, we set the user and create the invoice
		if(array_key_exists('response', $response) && $response['response'] == '1') {
			$purchase = $this->transaction->purchase($response);
			if(!$purchase) {
				return response()->error($this->transaction->errors());
			}
			$response = array_merge($response, $purchase);
			Event::fire(new CheckoutEvent($response));
		} else {
			$this->transaction->addLead($response);
			Event::fire(new CheckoutFailedEvent($response));
			Log::info('Merchant', $response);
			if(!array_key_exists('response', $response)) {
				$response['gateway_message'] = 'There is a problem with the Merchant. Please contact support to solve the problem!';
			}
			return response()->error($response['gateway_message']);
		}

		$token = $this->generateToken($response['user_id']);
		return response()->ok(array_merge($response, compact('token')));
	}

	/**
	 *
	 * Used by the admin when a transaction goes to the gateway
	 * and it failing after that
	 *
	 * @param Request $request
	 * @return mixed
	 */
	public function fallback(Request $request)
	{
		$id = $request->id;
		$transaction = $this->transaction->find($id);
		$response = $this->transaction->fallback(array_merge($transaction->toArray(), $request->all()));
		if(!$response) {
			return response()->error($this->transaction->errors());
		}

		$purchase = $this->transaction->purchase($response);
		if(!$purchase) {
			return response()->error($this->transaction->errors());
		}

		$response = array_merge($response, $purchase);
		Event::fire(new CheckoutEvent($response));
		$token = $this->generateToken($response['user_id']);
		return response()->ok(array_merge($response, compact('token')));
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
		if(array_key_exists('offer_id', $fields)) {
			$fields['offer_id'] = is_string($fields['offer_id']) ? explode(',', $fields['offer_id']) : $fields['offer_id'];
		}

		$response = $this->transaction->upgrade($fields);
		if(!$response) {
			return response()->error($this->transaction->errors());
		}

		// if everything is ok, we set the user and create the invoice
		if(array_key_exists('response', $response) && $response['response'] == '1') {
			$purchase = $this->transaction->purchase($response);
			if(!$purchase) {
				return response()->error($this->transaction->errors());
			}
			$response = array_merge($response, $purchase);
			Event::fire(new CheckoutEvent($response));
		}
		else {
			Log::info('Merchant', $response);
			if(!array_key_exists('response', $response)) {
				$response['gateway_message'] = 'There is a problem with the Merchant. Please contact support to solve the problem!';
			}
			return response()->error($response['gateway_message']);
		}

		$token = $this->generateToken($response['user_id']);
		return response()->ok(array_merge($response, compact('token')));
	}

	public function refund(Request $request)
	{
		$transaction_id = $request->id;
		$response = $this->transaction->refund($transaction_id);
		if(!$response) {
			return response()->error($this->transaction->errors());
		}

		$response['admin_id'] = $request->user()->id;
		Event::fire(new RefundEvent($response));
		return response()->ok($response);
	}

	protected function generateToken($user_id)
	{
		try {
			$token = Token::add($user_id);
		} catch (JWTException $e) {
			return response()->error('Could not create a token', $e->getStatusCode());
		}
		return $token;
	}
}
