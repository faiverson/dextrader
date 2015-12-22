<?php

namespace App\Http\Controllers;

use App\Models\UserSettings;
use Auth;
//use App\Repositories\UserRepository;
use App\Repositories\Contracts\UserRepositoryInterface as UserRepository;
use Datatables;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use DB;
use Beautymail;
use Hash;
use Token;
use App\Libraries\eWallet\eWallet;

class UsersController extends Controller
{
	private $user;

	public function __construct(UserRepository $user)
	{
		$this->user = $user;
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
        $user = new User();
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users|max:150',
            'username' => 'required|unique:users|max:50',
			'phone' => 'sometimes|required|numeric|min:8|max:20',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->error($validator->errors()->all());
        }
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->username = $request->input('username');
		$user->phone = $request->input('phone');
        $user->password = bcrypt($request->input('password'));
        $user->save();
        return response()->ok((array('user_id' => $user->id)));
    }
//
//    /**
//     * Edit the form for editing the specified resource.
//     *
//     * @param  int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function update(Request $request)
//    {
//        $id = $request->input('id');
//		$first_name = $request->input('first_name');
//		$last_name = $request->input('last_name');
//		$email = $request->input('email');
//		$username = $request->input('username');
//		$phone = $request->input('phone');
//		$password = $request->input('password');
//
//		$user = User::find($id);
//		$update = [];
//		if($first_name) {
//			$update['first_name'] = $first_name;
//			$validations['first_name'] = 'required';
//		}
//
//		if($last_name) {
//			$update['last_name'] = $last_name;
//			$validations['last_name'] = 'required';
//		}
//
//		if($email) {
//			$update['email'] = $email;
//			$validations['email'] = 'required|email|unique:users,id,' . $user->id;
//		}
//
//		if($username) {
//			$update['username'] = $username;
//			$validations['username'] = 'required|unique:users,id,' . $user->id;
//		}
//
//		if($phone) {
//			$update['phone'] = $phone;
//		}
//
//		if($password) {
//			$validations['password'] = 'required';
//			$update['password'] = bcrypt($password);
//		}
//
//		if(!empty($validations)) {
//			$validator = Validator::make($update, $validations);
//			if ($validator->fails()) {
//				return response()->error($validator->errors()->all());
//			}
//		}
//
//        $user->update($update);
//        return response()->ok();
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     *
//     * @param  int $id
//     * @return \Illuminate\Http\Response
//     */
//    public function destroy(Request $request)
//    {
//        $id = $request->get('id');
//        User::destroy($id);
//        return response()->destroy();
//    }
//
//	/**
//	 * Create an eWallet account
//	 *
//	 * @param  Request $request object
//	 * @return \Illuminate\Http\Response
//	 */
//	public function createEwallet(Request $request)
//	{
//		$user = $request->user();
//		$userId = $user->id;
//		$eWallet = new eWallet($user);
//		$response = $eWallet->create();
//		// if the user account was created
//		if($response['code'] == 'NO_ERROR') {
//			// check if there is set in the system
//			$settings = UserSettings::where('user_id', $userId)->where('key', 'ewallet')->first();
//			if(empty($settings)) {
//				UserSettings::create([
//					'user_id' => $userId,
//					'key' => 'ewallet',
//					'value' => $user->username
//				]);
//			}
//			else {
//				$settings->value = $user->username;
//				$settings->save();
//			}
//		}
//		return response()->ok($response);
//	}
}
