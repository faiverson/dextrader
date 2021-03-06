<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Password;
use Illuminate\Mail\Message;
use Validator;
use Token;
use Auth;
use User;

class PasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

	protected $subject = 'Dex Trader Password Reset Request';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

	/**
	 * Send a reset link to the given user.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return json object
	 */
	public function postEmail(Request $request)
	{
		$this->validate($request, ['email' => 'required|email']);

		view()->composer('emails.password', function($view) {
		});

		$response = Password::sendResetLink($request->only('email'), function (Message $message) {
			$message->subject($this->getEmailSubject());
		});

		switch ($response) {
			case Password::RESET_LINK_SENT:
				return response()->ok();

			case Password::INVALID_USER:
				return response()->error(trans($response));
		}

	}

	/**
	 * Reset the given user's password.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function postReset(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'token' => 'required',
			'email' => 'required|email',
			'password' => 'required|confirmed|min:6',
			'password_confirmation' => 'required|min:6',
		]);

		if ($validator->fails()) {
			return response()->error($validator->errors());
		}

		$credentials = $request->only(
			'email', 'password', 'password_confirmation', 'token'
		);
		$token = null;

		$response = Password::reset($credentials, function ($user, $password) {
			$this->resetPassword($user, $password);
		});

		if($response == Password::INVALID_TOKEN) {
			return response()->error('The token is not longer valid');
		}

		if($response == Password::PASSWORD_RESET) {
			$user = User::where('email', $credentials['email'])->first();
			if(!$user) {
				return response()->error('Email is not valid');
			}

			$token = Token::add($user->id);
			return response()->ok(compact('token'));
		}

		return response()->error('Error: ' . $response);
	}

	/**
	 * @param $user
	 * @param $password
	 */
	protected function resetPassword($user, $password)
	{
		// the model will encrypt the pwd
		$user->password = $password;
		$user->save();
	}
}
