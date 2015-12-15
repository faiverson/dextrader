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
		$first_six = substr($number, 0, 6);
		$last_four = substr($number, -4);
		if (!$card['valid']) {
			return response()->error('The credit card number is not valid number');
		}
		if(CreditCard::where('number', Encrypt::encrypt($number))->count() > 0) {
			return response()->error('The credit card is in the system! Please contact support immediately!');
		}

		$enroller_id = $this->getEnroller($fields['enroller']);
		$product = Product::find($fields['product_id'])->first();
		$geo = $this->getGeoIP($request);
		$role = Role::where('name', $product->name)->first(array('id'));
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

			BillingAddress::create([
				'user_id' => $user->id,
				'address' => $fields['address'],
				'address2' => empty($fields['address2']) ? '' : $fields['address2'],
				'city' => $fields['city'],
				'state' => $fields['state'],
				'country' => $fields['country'],
				'zip' => $fields['zip'],
				'phone' => empty($fields['billing_phone']) ? null : $fields['billing_phone'],
			]);
			CreditCard::create([
				'user_id' => $user->id,
				'name' => $fields['card_name'],
				'exp_month' => $fields['month'],
				'exp_year' => $fields['year'],
				'number' => Encrypt::encrypt($number),
				'network' => $type ? $type : $card['type'],
				'first_six' => $first_six,
				'last_four' => $last_four
			]);
			$purchase = Purchase::create([
				'user_id' => $user->id,
				'enroller_id' => $enroller_id,
				'funnel_id' => $fields['funnel_id'],
				'product_id' => $fields['product_id'],
				'product_amount' => $product->amount,
				// billing address
				'billing_address' => $fields['address'],
				'billing_address2' => empty($fields['address2']) ? '' : $fields['address2'],
				'billing_city' => $fields['city'],
				'billing_state' => $fields['state'],
				'billing_country' => $fields['country'],
				'billing_zip' => $fields['zip'],
				'billing_phone' => empty($fields['billing_phone']) ? null : $fields['billing_phone'],
				// CC data
				'card_name' => $fields['card_name'],
				'card_exp_month' => $fields['month'],
				'card_exp_year' => $fields['year'],
				'card_network' => $type ? $type : $card['type'],
				'card_first_six' => $first_six,
				'card_last_four' => $last_four,
				'info' => json_encode($fields['data']),
			]);

//			if($enroller_id) {
//				$this->apply_commissions($purchase);
//			}
		} catch(\Exception $e) {
			DB::rollback();
			throw $e;
		}
		DB::commit();

		$user->email = 'fa.iverson@gmail.com';
		$params = [
			'user' => $user,
			'purchase' => $purchase,
			'product' => $product
		];
		$beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
		$beautymail->send('emails.purchase', $params, function ($message) use ($user) {
			$message
				->from('system@dextrader.com')
				->to($user->email)
				->subject('Yey! Your purchase has been approved!');
		});
		return response()->added();
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
			'product_id' => 'required|exists:products,id',
			'mk_id' => 'required|exists:marketing_links,id'
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
			'product_id' => $fields['product_id'],
			'funnel_id' => $fields['mk_id']
		]);

		$this->apply_commissions($purchase);
        return response()->added();
    }

	public function apply_commissions($purchase)
	{
		$product = Product::find($purchase->product_id);
		if($product) {
			// the enroller taken by the purchase give us the chance
			// to set different people if that's the case
			Commission::create([
				'from_user_id' => $purchase->user_id,
				'to_user_id' => $purchase->enroller_id,
				'purchase_id' => $purchase->id,
				'amount' => $product->amount * Config::get('dextrader.comms'),
			]);
		}
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
