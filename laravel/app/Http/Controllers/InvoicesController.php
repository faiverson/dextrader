<?php

namespace App\Http\Controllers;

use App\Gateways\InvoiceGateway;
use Illuminate\Http\Request;
use Config;

class InvoicesController extends Controller
{
	public function __construct(InvoiceGateway $gateway)
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
	public function index(Request $request)
	{
		$id = $request->id;
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$response = $this->gateway->findBy('user_id', $id, null, $limit, $offset);
		return response()->ok($response);
	}

}
