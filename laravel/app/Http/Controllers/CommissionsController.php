<?php

namespace App\Http\Controllers;

use App\Gateways\CommissionGateway;
use Hit;
use Illuminate\Http\Request;
use App\Http\Requests;
use Config;

class CommissionsController extends Controller
{

	protected $gateway;

	public function __construct(CommissionGateway $gateway)
	{
		$this->gateway = $gateway;
		$this->limit = Config::get('dextrader.limit');
	}

	/**
	 * Show array of user commissions
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$id = $request->id;
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$order_by = $request->input('order') ? $request->input('order') : ['asset' => 'asc'];
		$response = $this->gateway->findBy('to_user_id', $id, null, $limit, $offset, $order_by);
		return response()->ok($response);
	}
}
