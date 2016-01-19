<?php

namespace App\Http\Controllers;

use App\Gateways\LiveSignalGateway;
use Illuminate\Http\Request;
use Config;
use Event;
use DateTime;
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
		$data = $this->parse($request->all());
		if(in_array(strtolower($data['type_product']), $this->types)) {
			return $this->store($data, $data['type_product']);
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
		$response = $this->gateway->add($data, $type);
		if(!$response) {
			return response()->error($this->gateway->errors());
		}
		return response()->ok($response);
	}

	public function update_by_page(Request $request)
	{
		$mt_id = $request->signal_id;
		$data = $this->parse($request->all());
		if(empty($mt_id)) {
			return response()->error('Missing MT-ID');
		}

		if(!array_key_exists('type_product', $data)) {
			return response()->error('Missing type product');
		}

		if(!array_key_exists('trade_type', $data)) {
			return response()->error('Missing trade type');
		}

		// remove everything else
		$data = [
			'trade_type' => array_key_exists('trade_type', $data) ? $data['trade_type'] : null,
			'type_product' => array_key_exists('type_product', $data) ? $data['type_product'] : null,
			'close_time' => array_key_exists('close_time', $data) ? $data['close_time'] : null,
			'close_price' => array_key_exists('close_price', $data) ? $data['close_price'] : null,
			'winloss' => array_key_exists('winloss', $data) ? $data['winloss'] : null
		];

		$signal = $this->gateway->find_signal($mt_id, $data['trade_type'], $data['type_product']);
		if(!$signal) {
			return response()->error('The signal is not in database');
		}
		return $this->update($signal->id, $data['type_product'], $data);
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

	protected function parse(array $data)
	{
		if(array_key_exists('expiry_time', $data)) {
			$data['signal_date'] = str_replace('.', '-', $data['signal_date']);
		}

		if(array_key_exists('expiry_time', $data)) {
			$data['expiry_time'] = str_replace('.', '-', $data['expiry_time']);
		}

		if(array_key_exists('close_time', $data)) {
			$data['close_time'] = str_replace('.', '-', $data['close_time']);
		}

		if(array_key_exists('type_product', $data)) {
			$data['type_product'] = strtolower($data['type_product']);
			if($data['type_product'] == 'nadex') {
				$data['type_product'] = 'na';
			}
			elseif($data['type_product'] == 'dibs') {
				$data['type_product'] = 'ib';
			}
			if($data['type_product'] == 'forex') {
				$data['type_product'] = 'fx';
			}
		}
		if(array_key_exists('trade_type', $data)) {
			$data['trade_type'] = strtoupper($data['trade_type']);
		}

		return $data;
	}
}
