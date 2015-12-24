<?php

namespace App\Http\Controllers;

use App\Gateways\TransactionGateway;
use App\Gateways\PurchaseGateway;
use Illuminate\Http\Request;
use Config;

class PurchasesController extends Controller
{
	public function __construct(TransactionGateway $gateway)
	{
		$this->transaction = $gateway;
		$this->limit = Config::get('dextrader.limit');
	}
	/**
	 * Show array of user cards
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request, PurchaseGateway $purchase)
	{
		$id = $request->id;
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$response = $purchase->findBy('user_id', $id, null, $limit, $offset);
		return response()->ok($response);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function checkout(Request $request)
	{
		$fields = $fields =$request->all();
		$fields['ip_address'] = $request->ip();
		$transaction = $this->transaction->add($fields);
		if(!$transaction) {
			return response()->error($this->transaction->errors());
		}

		// if everything is ok, we set the user and create the invoice
		if(strtolower($transaction['responsetext']) == 'success') {
			$transaction = $this->transaction->purchase($transaction);
			if(!$transaction) {
				return response()->error($this->transaction->errors());
			}
		}

		return response()->ok($transaction);
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

	public function applyCommissions(Purchase $purchase)
	{
		// the enroller taken by the purchase give us the chance
		// to set different people if that's the case
		if($purchase->enroller_id > 0) {
			$comms = Commission::create([
				'from_user_id' => $purchase->user_id,
				'to_user_id' => $purchase->enroller_id,
				'purchase_id' => $purchase->id,
				'amount' => $purchase->amount * Config::get('dextrader.comms'),
			]);
			$parent = User::find($purchase->enroller_id);
			if($parent->enroller_id > 0) {
				Commission::create([
					'from_user_id' => $purchase->user_id,
					'to_user_id' => $parent->enroller_id,
					'purchase_id' => $purchase->id,
					'type' => 'parent',
					'amount' => $purchase->amount * Config::get('dextrader.parent_comms'),
				]);
			}
			return $comms;
		}

		return false;
	}

}
