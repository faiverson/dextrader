<?php

namespace App\Http\Controllers;

use Auth;
use User;
use Datatables;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
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

    public function index(Request $request)
    {
        $id = $request->get('id');
//        $raw = DB::raw('CASE role
//                    WHEN 1 THEN "Usuario"
//                    WHEN 2 THEN "Editor"
//                    WHEN 3 THEN "Administrador"
//                    END AS role ');
//        $query = DB::table('users')
//            ->select('id', 'first_name', 'last_name', 'username', 'email', 'created_at', 'updated_at', $raw)
//            ->where('id', '!=', Auth::user()->id);
//        return Datatables::of($query)->make(true);
        if (isset($id)) {
            $user = User::with('role')->where('id', $request->get('id'))->where('active', 1)->first();
        } else {

            $user = User::with('role')->where('active', 1)->get();
        }

        return response()->info($user);
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
            'password' => 'required',
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->error($validator->errors()->all());
        }
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->email = $request->get('email');
        $user->username = $request->get('username');
        $user->password = bcrypt($request->get('password'));
        $user->role_id = $request->get('role_id');
        $user->save();
        return response()->info();
    }

    /**
     * Edit the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $id = $request->get('id');
        $user = User::find($id);
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users,id,' . $user->id,
            'username' => 'required|unique:users,id,' . $user->id,
            'role_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->error($validator->errors()->all());
        }

        $user->update([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'email' => $request->get('email'),
            'username' => $request->get('username'),
            'password' => bcrypt($request->get('password')),
            'role_id' => $request->get('role_id')
        ]);
        return response()->info();
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
        return response()->info();
    }
}
