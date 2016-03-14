<?php

namespace App\Http\Controllers;

use App\Gateways\UserGateway;
use App\Models\UserSettings;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Libraries\eWallet\eWallet;
use Config;
use Tymon\JWTAuth\Exceptions\JWTException;

class UsersController extends Controller
{
	private $gateway;

	public function __construct(UserGateway $gateway)
	{
		$this->gateway = $gateway;
		$this->limit = Config::get('dextrader.limit');
	}

    public function show(Request $request)
    {
		$user = $request->user();
		$id = $request->id;
        if (isset($id)) {
			if($user->id != $id && !$user->can(['user.profile'])) {
				return response()->error('User does not have permission user.profile');
			}
			return response()->ok($this->gateway->findById($id));
        } else {
			return response()->error('User not found');
		}
    }

	public function index(Request $request)
	{
		$user_id = $request->user()->id;
		$select = ['id', 'username', 'first_name', 'last_name', 'email', 'created_at'];
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$order_by = $request->input('order') ? $request->input('order') : ['id' => 'desc'];
		$filters = $request->input('filter') ? $request->input('filter') : [];
		$filters['user_id'] = $user_id;
		$users = $this->gateway->actives($select, $limit, $offset, $order_by, $filters);
		$total = $this->gateway->totalActives($filters);
		return response()->ok([
			'users' => $users,
			'total' => $total
		]);
	}

	public function downline(Request $request)
	{
		$id = $request->id;
		$select = ['id', 'username', 'first_name', 'last_name', 'phone',  'email', 'created_at'];
		$limit = $request->input('limit') ? $request->input('limit') : $this->limit;
		$offset = $request->input('offset') ? $request->input('offset') : 0;
		$order_by = $request->input('order') ? $request->input('order') : ['id' => 'desc'];
		$filters = $request->input('filter') ? $request->input('filter') : [];
		$users = $this->gateway->getUserBySponsor($id, $select, $limit, $offset, $order_by, $filters);
		$total = $this->gateway->getTotalUserBySponsor($id, $filters);
		return response()->ok([
			'users' => $users,
			'total' => $total
		]);
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
		$user = $this->gateway->add($fields);
		if(!$user) {
			return response()->error($this->gateway->errors()->all());
		}

		try {
			$token = Token::add($user->id);
		}
		catch (JWTException $e) {
			return response()->error('Could not create a token', $e->getStatusCode());
		}

		return response()->ok(array_merge($user, compact('token')));
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
		$fields['admin'] = $request->ip();
		$user = $this->gateway->edit($fields, $id, $request->user());
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
		$id = $request->id;
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
