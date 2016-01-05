<?php

namespace App\Http\Middleware;

use Closure;
use Token;
use User;
/**
 * Class IsUser
 *
 * This class is in charge to check if the user that it is requesting the info
 * is the same that have access
 *
 * @package App\Http\Middleware
 */
class IsUser
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next)
	{
		$userId = Token::getId($request);
		if($userId != $request->id ) {
			$user = User::find($userId);
			if(!$user->hasRole('admin') && !$user->hasRole('owner')) {
				return response()->error('Unauthorized', 401);
			}
		}

		return $next($request);
	}
}
