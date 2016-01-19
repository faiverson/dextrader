<?php

namespace App\Http\Controllers;

use App\Gateways\PaymentGateway;
use Illuminate\Http\Request;
use App\Http\Requests;
use Config;

class PaymentsController extends Controller
{

	protected $gateway;

	public function __construct(PaymentGateway $gateway)
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
		$payments = $this->gateway->getUserPayments($id, $limit, $offset, $order_by, $filters);
		$total = $this->gateway->getTotalUserPayments($id, $filters);
		return response()->ok([
			'payments' => $payments,
			'total' => $total
		]);
	}
}
