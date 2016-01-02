<?php

namespace App\Http\Controllers;

use App\Gateways\LiveSignalGateway;
use Illuminate\Http\Request;
use Config;
use Event;

class LiveSignalsController extends Controller
{
	public function __construct(LiveSignalGateway $live_gateway)
	{
		$this->live_gateway = $live_gateway;
		$this->limit = Config::get('dextrader.limit');
	}

	/**
	 * Show array of user cards
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function all_live(Request $request)
	{
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$order_by = $request->input('order') ? $request->input('order') : ['signal_date' => 'desc', 'signal_time' => 'desc', 'asset' => 'asc'];
		$response = $this->live_gateway->all(null, $limit, $offset, $order_by);
		return response()->ok($response);
	}

	public function show(Request $request)
	{
		$id = $request->signal_id;
		$response = $this->live_gateway->find($id);
		return response()->ok($response);
	}

	public function store_by_page(Request $request)
	{
		$type = $request->input('type');
		if($type == 'live') {
			return $this->store($request->all(), 'live');
		}
		return response()->error('You need to set a type');
	}

	public function store_live(Request $request)
	{
		return $this->store($request->all(), 'live');
	}

	public function store($data, $type)
	{
		$response = false;
		if($type == 'live') {
			$response = $this->live_gateway->create($data);
		}
		if(!$response) {
			return response()->error($this->live_gateway->errors());
		}
		return response()->ok($response);
	}

	public function update_by_page(Request $request)
	{
		$signal_id = $request->signal_id;
		$type = $request->input('type');
		$data = [
			'expiry_time' => $request->input('expiry_time'),
			'end_price' => $request->input('end_price')
		];
		if($type == 'live') {
			return $this->update($signal_id, $type, $data);
		}
		return response()->error('You need to set a type');
	}

	public function update_live(Request $request)
	{
		$signal_id = $request->signal_id;
		return $this->update($signal_id, 'live', $request->all());
	}

	/**
	 * @param $signal_id
	 * @param $type live for now, we wait for more
	 * @param $data
	 * @return mixed
	 */
	public function update($signal_id, $type, $data)
	{
		$response = false;
		if($type == 'live') {
			$response = $this->live_gateway->update($data, $signal_id);
		}

		if(!$response) {
			return response()->error($this->live_gateway->errors());
		}
		return response()->ok($response);
	}

	public function destroy_live(Request $request)
	{
		$signal_id = $request->signal_id;
		return $this->destroy($signal_id, 'live');
	}

	public function destroy($signal_id, $type)
	{
		$response = false;
		if($type == 'live') {
			$response = $this->live_gateway->destroy($signal_id);
		}

		if(!$response) {
			return response()->error($this->live_gateway->errors());
		}
		return response()->ok($response);
	}

}
