<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use Illuminate\Support\Facades\Config;
use User;
use Illuminate\Http\Request;
use Validator;
use Purchase;
use Token;
use Commission;

class PurchasesController extends Controller
{
	/**
	 * Show array of user cards
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$id = $request->id;
		$cards = Purchase::where('user_id', $id)->get();
		return response()->ok($cards);
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function purchase(Request $request)
    {
        $rules = [
			'card_id' => 'required|exists:credit_cards,id',
			'product_id' => 'required|exists:products,id'
		];
		$fields = $request->all();
		if(!empty($fields['enroller_id'])) {
			$rules['enroller_id'] = 'required|exists:users,id';
		}

        $validator = Validator::make($fields, $rules);
        if ($validator->fails()) {
            return response()->error($validator->errors()->all());
        }

		// lets check if it's the owner
		$user_id = Token::getId($request);
		$belong_user = $this->checkCard($fields['card_id'], $user_id);
		if($belong_user <= 0) {
			return response()->error('The CC does not belong to the user');
		}

		$purchase = Purchase::create([
			'user_id' => $user_id,
			'enroller_id' => $fields['enroller_id'],
			'card_id' => $fields['card_id'],
			'product_id' => $fields['product_id']
		]);

//		$this->apply_commissions($purchase);

        return response()->added();
    }

	public function apply_commissions($purchase)
	{
		Commission::create([
			'user_id' => $purchase->user_id,
			'amount' => $purchase->user_id * Config::get('dextrader.comms')
		]);
	}

    /**
     * Remove CC softly
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function checkCard($card_id, $user_id)
    {
        return CreditCard::where('id', $card_id)->where('user_id', $user_id)->count();
    }
}
