<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Gateways\StatsGateway;
use Config;

class StatsController extends Controller
{

    protected $gateway;

    public function __construct(StatsGateway $gateway)
    {
        $this->gateway = $gateway;
		$this->limit = Config::get('dextrader.limit');
    }

    /**
     * Show array of testimonials
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function marketing(Request $request)
    {
		$id = $request->id;
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$order_by = $request->input('order') ? $request->input('order') : ['id' => 'desc'];
		$filters = $request->input('filter') ? $request->input('filter') : [];
		$stats = $this->gateway->getMarketingStats($id, $limit, $offset, $order_by, $filters);
		$total = $this->gateway->getTotalMarketingStats($id, $filters);
		return response()->ok([
			'stats' => $stats,
			'total' => $total
		]);
    }

}