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
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkout(Request $request)
	{
		$fields = $fields =$request->all();
		$fields['ip_address'] = $request->ip();
		$transaction = $this->transaction->add($fields);
		if(!$transaction) {
			return response()->error($this->transaction->errors());
		}

		// if everything is ok, we set the user and create the invoice
		if(strtolower($transaction['responsetext']) == 'success') {
			$transaction = $this->transaction->purchase($transaction);
			if(!$transaction) {
				return response()->error($this->transaction->errors());
			}
			Event::fire(new CheckoutEvent($transaction));
		}

		return response()->ok($transaction);
	}


}
