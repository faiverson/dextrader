<?php

namespace App\Http\Middleware;

use Closure;
use Token;
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
		$sub = Token::getId($request);
		if($sub != $request->id) {
			return response()->error('Unauthorized', 401);
		}

		return $next($request);
	}
}
