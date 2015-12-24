<?php

namespace App\Http\Controllers;

use App\Gateways\TransactionGateway;
use App\Gateways\PurchaseGateway;
use Illuminate\Http\Request;
use Config;
use Event;

class PurchasesController extends Controller
{
	public function __construct(PurchaseGateway $gateway)
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

    public function emailPurchase(Purchase $purchase)
	{
		$purchase->email = 'fa.iverson@gmail.com'; // @TODO remove please
		$beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
		$beautymail->send('emails.purchase', ['purchase' => $purchase], function ($message) use ($purchase) {
			$message
				->from(Config::get('dextrader.from'))
				->to($purchase->email)
				->subject('Yey! Your purchase has been approved!');
		});
	}

	public function emailComms(User $user)
	{
//		$purchase->email = 'fa.iverson@gmail.com'; // @TODO remove please
//		$beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
//		$beautymail->send('emails.comms', ['purchase' => $purchase], function ($message) use ($purchase) {
//			$message
//				->from(Config::get('dextrader.from'))
//				->to($purchase->email)
//				->subject('Yey! Your receive a new commission!');
//		});
	}


}
