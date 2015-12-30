<?php

namespace App\Http\Controllers;

use App\Gateways\LiveSignalGateway;
use Illuminate\Http\Request;
use Config;
use Event;

class LiveSignalsController extends Controller
{
	public function __construct(LiveSignalGateway $gateway)
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
	public function all(Request $request)
	{
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$order_by = $request->input('order') ? $request->input('order') : ['signal_date' => 'desc', 'signal_time' => 'desc', 'asset' => 'asc'];
		$response = $this->gateway->all(null, $limit, $offset, $order_by);
		return response()->ok($response);
	}

	public function store_by_page(Request $request)
	{
		$response = $this->gateway->add($request->all());
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}

	public function update_by_page(Request $request)
	{
		$signal_id = $request->signal_id;
		$response = $this->gateway->change([
			'expiry_time' => $request->input('expiry_time'),
			'end_price' => $request->input('end_price')
		], $signal_id);

		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}




}
