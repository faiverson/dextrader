<?php

namespace App\Http\Controllers;

use App\Gateways\CommissionGateway;
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
		$order_by = $request->input('order') ? $request->input('order') : ['id' => 'desc'];
		$filters = $request->input('filter') ? $request->input('filter') : [];
		$commissions = $this->gateway->getUserCommissions($id, $limit, $offset, $order_by, $filters);
		$total = $this->gateway->getTotalUserCommissions($id, $filters);
		return response()->ok([
			'commissions' => $commissions,
			'total' => $total
		]);
	}

	/**
	 * Show array of total commission
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function summary(Request $request)
	{
		$id = $request->id;
		$totals = $this->gateway->getSummaryUserCommissions($id);
		return response()->ok($totals);
	}

	public function balance(Request $request)
	{
		$id = $request->id;
		$response = $this->gateway->getBalance($id);
		return response()->ok($response);
	}
}
