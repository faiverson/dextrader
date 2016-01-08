<?php

namespace App\Http\Controllers;

use App\Gateways\SpecialOfferGateway;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SpecialOffersController extends Controller
{
	protected $gateway;

	public function __construct(SpecialOfferGateway $gateway)
	{
		$this->gateway = $gateway;
	}
	/**
	 * Display a listing of roles
	 *
	 */
	public function show(Request $request)
	{
//		$page = $request->header('Origin');
		$funnel_id = $request->funnel_id;
		if(!$funnel_id) {
			return response()->error('The funnel is missing');
		}
		$response = $this->gateway->findByFunnel($funnel_id);
		return response()->ok($response);
	}
}
