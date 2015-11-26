<?php

namespace App\Http\Controllers;

use Auth;
use User;
use Datatables;
use Illuminate\Http\Request;
use App\Http\Requests;
use Validator;
use DB;
use Beautymail;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function contact(Request $request)
    {
        $name = $request->get('name');
        $phone = $request->get('phone');
        $email = $request->get('email');
        $subject = $request->get('subject');
        $msg = $request->get('message');
        $params = array(
            'name' => $name,
            'phone' => $phone,
            'email' => $email,
            'subject' => $subject,
            'msg' => $msg
        );
        $beautymail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $beautymail->send('emails.contact', $params, function ($message) {
            $message
                ->from('system@xxx.com.ar')
                ->to('info@xxx.com.ar')
                ->subject('Yey! A new question!');
        });

        return array('success' => true);
    }

    public function show($id)
    {
		$user = Auth::user();
        if (isset($id)) {
			if($user->id != $id && !$user->can(['user.profile'])) {
				return response()->error('User does not have permission user.profile');
			}
            $user = User::with('roles')->where('id', $id)->where('active', 1)->first();
			return response()->ok($user);
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

		$order_by = $order[0] ? $order[0] : array('column' => 0, 'dir' => 'desc');
		$sort = ['id', 'first_name', 'last_name', 'username', 'email', 'created_at', 'updated_at'];
		$fields = ['s.id', 'l.name as product', 's.datetime as created_at', 's.last_billing', 's.status'];

		$order_by['column'] = $sort[$order_by['column']];
		$query = User::where('active', 1)
			->with('roles')
			//->skip($start)
			//->take($length)
			->orderBy($order_by['column'], $order_by['dir'])
			->get();

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
            'email' => 'required|email|unique:users|',
            'username' => 'required|unique:users',
            'password' => 'required'
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
        return response()->added();
    }

    /**
     * Edit the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
		$first_name = $request->input('first_name');
		$last_name = $request->input('last_name');
		$email = $request->input('email');
		$username = $request->input('username');
		$phone = $request->input('phone');
		$password = $request->input('password');

		$user = User::find($id);
		$update = [];
		if($first_name) {
			$update['first_name'] = $first_name;
			$validations['first_name'] = 'required';
		}

		if($last_name) {
			$update['last_name'] = $last_name;
			$validations['last_name'] = 'required';
		}

		if($email) {
			$update['email'] = $email;
			$validations['email'] = 'required|email|unique:users,id,' . $user->id;
		}

		if($username) {
			$update['username'] = $username;
			$validations['username'] = 'required|unique:users,id,' . $user->id;
		}

		if($phone) {
			$update['phone'] = $phone;
		}

		if($password) {
			$validations['password'] = 'required';
			$update['password'] = bcrypt($password);
		}

		if(!empty($validations)) {
			$validator = Validator::make($update, $validations);
			if ($validator->fails()) {
				return response()->error($validator->errors()->all());
			}
		}

        $user->update($update);
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
        $id = $request->get('id');
        User::destroy($id);
        return response()->destroy();
    }
}
