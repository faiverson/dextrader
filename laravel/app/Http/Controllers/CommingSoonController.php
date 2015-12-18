<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CommingSoon;
use App\Http\Requests;
use Validator;

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
			'product_id' => 'required',
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

		$product_id = $fields['product_id'];
		// let's make sure the products are NA or FX
		if($product_id != 3 && $product_id != 4) {
			return response()->error('Wrong product ID');
		}

		$is = CommingSoon::where('user_id', $fields['user_id'])->where('product_id', $fields['product_id'])->count();
		if($is > 0) {
			return response()->error('The user is subscribe already!');
		}
		$cs = CommingSoon::create($fields);
		return response()->added();
	}
}
