<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CommingSoon;
use App\Http\Requests;
use Validator;
use Product;

class CommingSoonController extends Controller
{
    /**
     * Display a listing of roles
     *
     */
    public function index(Request $request)
    {
		$list = CommingSoon::with('users')->where('product_id', $request->product_id)->get();
		return response()->ok($list);
	}

	/**
	 * Display a listing of roles with the permissions
	 *
	 */
	public function addUser(Request $request)
	{
		$rules = [
			'product' => 'required',
			'email' => 'required'
		];
		$user = $request->user();
		$fields = $request->all();
		$fields['user_id'] = $user->id;

		if(!empty($fields['phone'])) {
			$rules['phone'] = ['regex:/^[0-9]{10,15}$/'];
		}

		$validator = Validator::make($fields, $rules);
		if ($validator->fails()) {
			return response()->error($validator->errors()->all());
		}

		$product = $fields['product'];
		// let's make sure the products are NA or FX
		if(!in_array(strtoupper($product), ['NA', 'FX'])) {
			return response()->error('Wrong product');
		}
		$product = Product::where('name', $product)->first();

		$is = CommingSoon::where('user_id', $fields['user_id'])->where('product_id', $product->id)->count();
		if($is > 0) {
			return response()->error('User already subscribed!');
		}
		$fields['product_id'] = $product->id;
		$cs = CommingSoon::create($fields);
		return response()->added();
	}
}
