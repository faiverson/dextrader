<?php

namespace App\Http\Controllers;

use App\Gateways\UserGateway;
use App\Models\UserSettings;
use Datatables;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Libraries\eWallet\eWallet;

class UsersController extends Controller
{
	private $gateway;

	public function __construct(UserGateway $gateway)
	{
		$this->gateway = $gateway;
	}

    public function show(Request $request)
    {
		$user = $request->user();
		$id = $request->id;
        if (isset($id)) {
			if($user->id != $id && !$user->can(['user.profile'])) {
				return response()->error('User does not have permission user.profile');
			}
			return response()->ok($this->user->findById($id));
        } else {
			return response()->error('User not found');
		}
    }

	public function index(Request $request)
	{
		$draw = $request->input('draw');
		$start = $request->input('start') ? $request->input('start') : 0;
		$length = $request->input('length') ? $request->input('length') : 10;
		$order = $request->input('order');
//		$list = $this->user->all(null, 30, 0, ['first_name' => 'asc', 'last_name' => 'desc']);
		$query = $this->user->actives(null, $length, $start, $order);
		return Datatables::of($query)->make(true);
	}

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$fields = $request->all();
		$fields['ip_address'] = $request->ip();
		$user = $this->gateway->create($fields);
		if(!$user) {
			return response()->error($this->gateway->errors()->all());
		}
        return response()->ok((array('user_id' => $user->id)));
    }

    /**
     * Edit the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
		$id = $request->id;
		$fields = $request->all();
		$fields['ip_address'] = $request->ip();
		$user = $this->gateway->edit($fields, $id);
		if(!$user) {
			return response()->error($this->gateway->errors()->all());
		}
        return response()->ok();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
		$id = $request->user()->id;
		$this->gateway->destroy($id);
        return response()->ok();
    }

	/**
	 * Create an eWallet account
	 *
	 * @param  Request $request object
	 * @return \Illuminate\Http\Response
	 */
	public function createEwallet(Request $request)
	{
		$user = $request->user();
		$userId = $user->id;
		$eWallet = new eWallet($user);
		$response = $eWallet->create();
		// if the user account was created
		if($response['code'] == 'NO_ERROR') {
			// check if there is set in the system
			$settings = UserSettings::where('user_id', $userId)->where('key', 'ewallet')->first();
			if(empty($settings)) {
				UserSettings::create([
					'user_id' => $userId,
					'key' => 'ewallet',
					'value' => $user->username
				]);
			}
			else {
				$settings->value = $user->username;
				$settings->save();
			}
		}
		return response()->ok($response);
	}
}
