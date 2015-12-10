<?php

namespace App\Http\Controllers;

use CreditCard;
use App\Models\UserSettings;
use User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use DB;
use Cards;
use Encrypt;

class CardsController extends Controller
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
		$cards = CreditCard::where('user_id', $id)->get();
		return response()->ok($cards);
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'month' => ['regex:/^(0?[1-9]|1[012])$/'],
			'year' => ['regex:/^[0-9]{2}$/'],
			'number' => ['regex:/^[0-9]{14,16}$/']
        ]);

        if ($validator->fails()) {
            return response()->error($validator->errors()->all());
        }

		$cc = new CreditCard;
		$number = $request->input('number');
		$type = $request->input('type');
		$card = Cards::validCreditCard($number, $type);
		if (!$card['valid']) {
			return response()->error('The credit card number is not valid number');
		}

		// check if the card is unique
		if(CreditCard::where('number', Encrypt::encrypt($number))->count() > 0) {
			return response()->error('The credit card is in the system');
		}

		$cc->user_id = $request->id;
		$cc->name = $request->input('name');
		$cc->exp_month = $request->input('month');
		$cc->exp_year = $request->input('year');
		$cc->number = Encrypt::encrypt($number);
		$cc->first_six = substr($number, 0, 6);
		$cc->last_four = substr($number, -4);
		$cc->network = $type ? $type : $card['type'];
        $cc->save();
        return response()->added();
    }

    /**
     * Edit CC
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
		$validator = Validator::make($request->all(), [
			'name' => 'required',
			'month' => ['regex:/^(0?[1-9]|1[012])$/'],
			'year' => ['regex:/^[0-9]{2}$/'],
			'number' => ['regex:/^[0-9]{14,16}$/']
		]);

		if ($validator->fails()) {
			return response()->error($validator->errors()->all());
		}

		$id = $request->id;
		$card_id = $request->card_id;
		$cc = CreditCard::where('id', $card_id)->where('user_id', $id)->first();
		if(empty($cc)) {
			return response()->error('The credit card does not belong to the user or is not in the system');
		}

		$number = $request->input('number');
		$type = $request->input('type');
		$card = Cards::validCreditCard($number, $type);
		// check if the number is valid
		if (!$card['valid']) {
			return response()->error('The credit card number is not valid number');
		}

		// check if the card is unique
		if(CreditCard::where('id', '!=', $card_id)->where('number', Encrypt::encrypt($number))->count() > 0) {
			return response()->error('The credit card is in the system');
		}

		$cc->name = $request->input('name');
		$cc->exp_month = $request->input('month');
		$cc->exp_year = $request->input('year');
		$cc->number = Encrypt::encrypt($number);
		$cc->network = $request->input('network');
		$cc->first_six = substr($number, 0, 6);
		$cc->last_four = substr($number, -4);
		$cc->network = $type ? $type : $card['type'];
		$cc->save();
        return response()->ok();
    }

    /**
     * Remove CC softly
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
		$card_id = $request->card_id;
		$cc = CreditCard::where('id', $card_id)->where('user_id', $id)->first();

		if(empty($cc)) {
			return response()->error('The credit card does not belong to the user or is not in the system');
		}
        $cc->delete();
        return response()->ok();
    }
}
