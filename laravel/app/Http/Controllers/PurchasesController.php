<?php

namespace App\Http\Controllers;

use CreditCard;
use Illuminate\Support\Facades\Config;
use User;
use Illuminate\Http\Request;
use Validator;
use Purchase;
use Token;
use Commission;
use Product;
use BillingAddress;
use Cards;
use Encrypt;
use DB;
use GeoIP;
use Role;

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
	public function checkout(Request $request)
	{
		$rules = [
			'first_name' => 'required',
			'last_name' => 'required',
			'email' => 'required|email|unique:users',
			'username' => 'required|unique:users',
			'password' => 'required',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required',
			'country' => 'required',
			'zip' => ['regex:/^[0-9]{4,5}$/'],
			'card_name' => 'required',
			'month' => ['regex:/^(0?[1-9]|1[012])$/'],
			'year' => ['regex:/^[0-9]{2}$/'],
			'number' => ['regex:/^[0-9]{14,16}$/'],
			'cvv' => ['regex:/^[0-9]{3,4}$/']
		];

		$fields = $request->all();
		if(!empty($fields['phone'])) {
			$rules['phone'] = ['regex:/^[0-9]{10,12}$/'];
		}

		if(!empty($fields['billing_phone'])) {
			$rules['billing_phone'] = ['regex:/^[0-9]{10,12}$/'];
		}

		$validator = Validator::make($fields, $rules);
		if ($validator->fails()) {
			return response()->error($validator->errors()->all());
		}

		$number = $fields['number'];
		$type = !empty($fields['type']) ? $fields['type'] : null;
		$card = Cards::validCreditCard($number, $type);
		$fields['type'] = $type ? $type: $card['type'];
		$first_six = substr($number, 0, 6);
		$last_four = substr($number, -4);
		if (!$card['valid']) {
			return response()->error('The credit card number is not valid number');
		}
		if(CreditCard::where('number', Encrypt::encrypt($number))->count() > 0) {
			return response()->error('The credit card is in the system! Please contact support immediately!');
		}

		$fields['enroller'] = !empty($fields['enroller']) ? $fields['enroller'] : '';
		$enroller_id = $this->getEnroller($fields['enroller']);
		$product = Product::find($fields['product_id'])->first();
		$geo = $this->getGeoIP($request);
		$role = Role::where('name', $product->name)->first(array('id'));
		$data = !empty($fields['data']) ? $fields['data'] : null;

		DB::beginTransaction();
		try {
			$user = User::create([
				'first_name' => $fields['first_name'],
				'last_name' => $fields['last_name'],
				'email' => $fields['email'],
				'username' => $fields['username'],
				'password' => bcrypt($fields['password']),
				'phone' => $fields['phone'] ? $fields['phone'] : null,
			]);
			$user->attachRole($role->id);

			$billing = BillingAddress::create([
				'user_id' => $user->id,
				'address' => $fields['address'],
				'address2' => empty($fields['address2']) ? '' : $fields['address2'],
				'city' => $fields['city'],
				'state' => $fields['state'],
				'country' => $fields['country'],
				'zip' => $fields['zip'],
				'phone' => empty($fields['billing_phone']) ? null : $fields['billing_phone'],
			]);
			$cc = CreditCard::create([
				'user_id' => $user->id,
				'name' => $fields['card_name'],
				'exp_month' => $fields['month'],
				'exp_year' => $fields['year'],
				'number' => Encrypt::encrypt($number),
				'network' => $fields['type'],
				'first_six' => $first_six,
				'last_four' => $last_four
			]);
			$fields['user_id'] = $user->id;
			$purchase = $this->purchase($user, $billing, $cc, $product, $fields['funnel_id'], $enroller_id, $data);
		} catch(\Exception $e) {
			DB::rollback();
			throw $e;
		}
		DB::commit();
		return response()->added();
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function purchase(User $user, BillingAddress $billing, CreditCard $cc, Product $product, $funnel_id, $enroller_id = null, $data = array())
    {
		$purchase = Purchase::create([
			'user_id' => $user->id,
			'enroller_id' => $enroller_id,
			'funnel_id' => $funnel_id,
			'product_id' => $product->product_id,
			'product_amount' => $product->amount,
			// billing address
			'billing_address' => $billing->address,
			'billing_address2' => empty($billing->address2) ? '' : $billing->address2,
			'billing_city' => $billing->city,
			'billing_state' => $billing->state,
			'billing_country' => $billing->country,
			'billing_zip' => $billing->zip,
			'billing_phone' => empty($billing->phone) ? null : $billing->phone,
			// CC data
			'card_name' => $cc->name,
			'card_exp_month' => $cc->exp_month,
			'card_exp_year' => $cc->exp_year,
			'card_network' => $cc->network,
			'card_first_six' => $cc->first_six,
			'card_last_four' => $cc->last_four,
			'info' => $data,
		]);

		if($enroller_id) {
			$this->applyCommissions($purchase);
		}

		$this->emailPurchase($purchase, $user, $product);
        return $purchase;
    }

	public function emailPurchase(Purchase $purchase, User $user, Product $product)
	{
		$user->email = 'fa.iverson@gmail.com';
		$params = [
			'user' => $user,
			'purchase' => $purchase,
			'product' => $product
		];
		$beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
		$beautymail->send('emails.purchase', $params, function ($message) use ($user) {
			$message
				->from(Config::get('dextrader.from'))
				->to($user->email)
				->subject('Yey! Your purchase has been approved!');
		});
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
				'amount' => $purchase->product_amount * Config::get('dextrader.comms'),
			]);
			return $comms;
		}

		return false;
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

	protected function getEnroller($enroller)
	{
		$enroller = User::where('username', $enroller)->first(array('id'));
		return $enroller ? $enroller->id : null;
	}

	protected function getGeoIP(Request $request)
	{
		return GeoIP::getLocation($request->ip());
	}
}
