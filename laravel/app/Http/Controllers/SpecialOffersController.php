<?php

namespace App\Http\Controllers;

use App\Gateways\SpecialOfferGateway;
use App\Gateways\MarketingLinkGateway;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class SpecialOffersController extends Controller
{
	protected $gateway;

	public function __construct(SpecialOfferGateway $gateway, MarketingLinkGateway $gatewayMK)
	{
		$this->gateway = $gateway;
		$this->gatewayMK = $gatewayMK;
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

		$products = $this->gatewayMK->getProducts($funnel_id);
		$offers = $this->gateway->findByFunnel($funnel_id);
		return response()->ok([
			'products' => $products,
			'offers' => $offers
		]);
	}
}
