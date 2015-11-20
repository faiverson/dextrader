<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use User;
use Validator;
use JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use DB;
use Hash;

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

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

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
					->where(function($q) use ($email, $username)
					{
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

				$user = User::with('roles.permissions')->find($u->id);
				if(!$user->hasRole(['owner', 'admin', 'editor'])) {
					return response()->error('Not Allowed', 401);
				}

				$customClaims = $user->toArray();
				$customClaims['iss'] = 'admin/login';
				$customClaims['exp'] = strtotime('+7 days', time());
				unset($customClaims['id']);
				$token = JWTAuth::fromUser($user, $customClaims);
				if($token) {
					return response()->ok(compact('token'));
				}

			}
			catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
				return response()->json(['Token expired'], $e->getStatusCode());
			}
			catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
				return response()->error('Token invalid', $e->getStatusCode());
			}
			catch (JWTException $e) {
				return response()->error('Could not create a token', $e->getStatusCode());
			}
		}
		return response()->error("The credentials are wrong", 400);
	}

	public function logout(Request $request)
	{
		$token = JWTAuth::setRequest($request)->getToken();
		JWTAuth::invalidate($token);
		return response()->ok();
	}

}
