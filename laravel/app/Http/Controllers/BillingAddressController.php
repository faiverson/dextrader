<?php

namespace App\Http\Controllers;

use App\Models\UserSettings;
use User;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use BillingAddress;
use DB;

class BillingAddressController extends Controller
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
		$bAddress = BillingAddress::where('user_id', $id)->get();
		return response()->ok($bAddress);
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$user_id = $request->id;
		$rules = [
			'name' => 'required',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required',
			'country' => 'required',
			'zip' => ['regex:/^[0-9]{4,5}$/']
		];
		$fields = $request->all();
		$fields['user_id'] = $user_id;
		if(!empty($fields['phone'])) {
			$rules['phone'] = ['regex:/^[0-9]{10,12}$/'];
		}

		$validator = Validator::make($fields, $rules);
        if ($validator->fails()) {
            return response()->error($validator->errors()->all());
        }

		DB::beginTransaction();
		try {
			if(BillingAddress::where('user_id', $user_id)->count() > 0) {
				$this->setDefault($user_id, 1);
			}

			$bAddress = BillingAddress::create($fields);
		} catch(\Exception $e) {
			DB::rollback();
			throw $e;
		}
		DB::commit();
        return response()->ok(array('address_id' => $bAddress->id));
    }

    /**
     * Edit CC
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
		$user_id = $request->id;
		$address_id = $request->address_id;
		$fields = $request->all();
		$rules = [
			'name' => 'required',
			'address' => 'required',
			'city' => 'required',
			'state' => 'required',
			'country' => 'required',
			'zip' => ['regex:/^[0-9]{4,5}$/']
		];

		if(!empty($fields['phone'])) {
			$rules['phone'] = ['regex:/^[0-9]{10,12}$/'];
		}

		$validator = Validator::make($fields, $rules);
		if ($validator->fails()) {
			return response()->error($validator->errors()->all());
		}

		$bAddress = BillingAddress::find($address_id);
		DB::beginTransaction();
		try {
			// we want only one default address but at least one
			if(!$bAddress->default_address) {
				$fields['default_address'] = !empty($fields['default_address']) ? $fields['default_address'] : 0;
				$this->setDefault($user_id, $fields['default_address']);
			}

			$bAddress->update($fields);
		} catch(\Exception $e) {
			DB::rollback();
			throw $e;
		}
		DB::commit();
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
		$address_id = $request->address_id;
		$bAddress = BillingAddress::where('id', $address_id)->where('user_id', $id)->first();

		if(empty($bAddress)) {
			return response()->error('The billing address does not belong to the user or is not in the system');
		}

		if($bAddress->default_address == 1) {
			return response()->error('Please select another default billing address before to delete this');
		}
		$bAddress->delete();
        return response()->ok();
    }

	/**
	 * Check if we want to change a default address in DB
	 * In that case we change all of them to 0
	 * because we can only have 1 default value
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function setDefault($user_id, $default = 0) {
		if($default == 1) {
			BillingAddress::where('user_id', $user_id)->update(['default_address' => 0]);
		}
	}
}
