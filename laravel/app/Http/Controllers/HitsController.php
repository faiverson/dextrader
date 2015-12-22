<?php

namespace App\Http\Controllers;

use Hit;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Gateways\HitGateway;

class HitsController extends Controller
{

	protected $gateway;

	public function __construct(HitGateway $gateway)
	{
		$this->gateway = $gateway;
	}

	/**
	 * Show array of user cards
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$query = $this->gateway->all();
		return response()->ok($query);
	}

	/**
	 * Show a user card
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
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
		$response = $this->gateway->create($fields);
		if(!$response) {
			return response()->error($this->gateway->errors()->all());
		}
		return response()->ok($response);
    }

}
