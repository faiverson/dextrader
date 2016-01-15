<?php

namespace App\Http\Controllers;

use App\Gateways\LiveSignalGateway;
use Illuminate\Http\Request;
use Config;
use Event;

class LiveSignalsController extends Controller
{
	protected $types = ['ib', 'na', 'fx'];

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
		$product = $request->product;
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$order_by = $request->input('order') ? $request->input('order') : ['signal_time' => 'desc', 'asset' => 'asc'];
		$signals = $this->gateway->all_signals($product, $limit, $offset, $order_by);
		$total = $this->gateway->total_signals($product);
		return response()->ok([
			'signals' => $signals,
			'total' => $total
		]);
	}

	public function show(Request $request)
	{
		$id = $request->signal_id;
		$product = $request->product;
		$response = $this->gateway->findByType($product, $id);
		return response()->ok($response);
	}

	public function store_by_page(Request $request)
	{
		$type = strtolower($request->input('type_product'));
		if(in_array($type, $this->types)) {
			return $this->store($request->all(), $type);
		}

		return response()->error('You need to set a product type');
	}

	public function store_signal(Request $request)
	{
		$product = $request->product;
		return $this->store($request->all(), $product);
	}

	public function store($data, $type)
	{
		if(array_key_exists('trade_type', $data)) {
			$data['trade_type'] = strtoupper($data['trade_type']);
		}

		$response = $this->gateway->add($data, $type);
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}

	public function update_by_page(Request $request)
	{
		$mt_id = $request->signal_id;
		$type = strtolower($request->input('type_product'));
		$trade = strtoupper($request->input('trade_type'));

		if(empty($mt_id) || empty($type) || empty($trade)) {
			return response()->error('Missing fields');
		}
		$data = [
			'close_price' => $request->input('close_price'),
			'winloss' => $request->input('winloss'),
		];

		$signal = $this->gateway->find_signal($mt_id, $trade, $type);
		if(!$signal) {
			return response()->error('The signal is not in database');
		}
		return $this->update($signal->id, $type, $data);
	}

	public function update_signal(Request $request)
	{
		$signal_id = $request->signal_id;
		$product = strtolower($request->product);
		return $this->update($signal_id, $product, $request->all());
	}

	/**
	 * @param $signal_id
	 * @param $type live for now, we wait for more
	 * @param $data
	 * @return mixed
	 */
	public function update($signal_id, $type, $data)
	{
		$response = $response = $this->gateway->edit($data, $signal_id, $type);
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}

	public function destroy(Request $request)
	{
		$signal_id = $request->signal_id;
		$product = $request->product;
		$response = $this->gateway->destroyByType($signal_id, $product);
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}

}
