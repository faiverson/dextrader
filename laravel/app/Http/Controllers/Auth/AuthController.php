<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use User;
use Validator;
use JWTAuth;
use Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;

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
		$email = $request->json('email');
		$username = $request->json('username');
		$password = $request->json('password');
		if(($email || $username)  && $password) {
			$user = $this->setUsernameLogin($email, $username);
			$user['password'] = $password;
			try {
				if(Auth::attempt($user)) {
					$user = Auth::user();
					$customClaims = $user->toArray();
					$customClaims['iss'] = 'login';
					$customClaims['exp'] = strtotime('+7 days', time());
					unset($customClaims['id']);
					$token = JWTAuth::fromUser($user, $customClaims);
					if($token) {
						return response()->ok(compact('token'));
					}
				}
				return response()->error('Invalid Credentials', 401);
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
		//Auth::logout();
		JWTAuth::invalidate(JWTAuth::getToken());
		return response()->ok();
	}

	protected function setUsernameLogin($email, $username)
	{
		if(empty($email)) {
			return array('username' => $username, 'password' => '');
		} else {
			return array('email' => $email, 'password' => '');
		}
	}

}