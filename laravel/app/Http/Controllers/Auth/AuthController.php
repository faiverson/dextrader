<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Validator;
use Auth;
use Illuminate\Http\Request;
use DB;
use Hash;
use Token;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use ThrottlesLogins;

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
		return Validator::make($data, [
			'username' => 'required|max:255|unique:users',
			'email' => 'required|email|max:255|unique:users',
			'password' => 'required|confirmed|min:6',
		]);
    }

	/**
	 * @param Request $request
	 * @return mixed
	 *
	 * We can set a email or username to create the authentication
	 * after this the session is created
	 *
	 */
	public function login(Request $request)
	{
		$email = $request->input('email');
		$username = $request->input('username');
		$password = $request->input('password');
		if(($email || $username)  && $password) {
			try {
				$u = DB::table('users')
					->select('id', 'password')
					->where('active', 1)
					->where(function($q) use ($email, $username) {
						$q->where('email', $email)
							->orWhere('username', $username);
					})
					->first();

				if(empty($u)) {
					return response()->error('Invalid Credentials', 401);
				}

				if(!Hash::check($password, $u->password)) {
					return response()->error('Wrong Password', 401);
				}

				$token = Token::add($u->id);
				if($token) {
					return response()->ok(compact('token'));
				}
				return response()->error('Invalid Credentials', 401);
			}
			catch (JWTException $e) {
				return response()->error('Could not create a token', $e->getStatusCode());
			}
		}
		return response()->error("The credentials are wrong", 400);
	}

	public function pages(Request $request)
	{
		$domain = $request->input('domain');
		$password = $request->input('password');
		if($domain  && $password) {
			try {
				$page = DB::table('pages')
					->select('id', 'password')
					->where('active', 1)
					->where('domain', $domain)
					->first();
				if(empty($page)) {
					return response()->error('Invalid Credentials', 401);
				}

				if(!Hash::check($password, $page->password)) {
					return response()->error('Wrong Password', 401);
				}

				$token = Token::page($page->id, '+20 years');
				if($token) {
					return response()->ok(compact('token'));
				}
				return response()->error('Invalid Credentials', 401);
			}
			catch (JWTException $e) {
				return response()->error('Could not create a token', $e->getStatusCode());
			}
		}
		return response()->error("The credentials are wrong", 400);
	}

	public function logout(Request $request)
	{
		$response = Token::deprecate($request);
		return !is_string($response) ? response()->ok() : response()->error($response);
	}

	public function loginAs(Request $request){
		$id = $request->id;
		if ($id) {
			try {
				$u = DB::table('users')
						->select('id', 'password')
						->where('active', 1)
						->where('id', $id)
						->first();

				if (empty($u)) {
					return response()->error('Invalid Credentials', 401);
				}

				$token = Token::add($u->id);
				if ($token) {
					return response()->ok(compact('token'));
				}
			} catch (JWTException $e) {
				return response()->error('Could not create a token', $e->getStatusCode());
			}
		}
		return response()->error("The credentials are wrong", 400);
	}
}
