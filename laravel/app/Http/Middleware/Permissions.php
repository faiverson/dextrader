<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Role;
/**
 * Class Roles
 *
 * Check permission for a role
 *
 * @package App\Http\Middleware
 */
class Permissions
{
	protected $auth;

	/**
	 * Creates a new instance of the middleware.
	 *
	 * @param Guard $auth
	 */
	public function __construct(Guard $auth)
	{
		$this->auth = $auth;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  Closure $next
	 * @param  $roles
	 * @return mixed
	 */
	public function handle($request, Closure $next, $perms)
	{
		if (!$request->user()->can(explode('|', $perms))) {
			return response()->error('The account being accessed does not have sufficient permissions to execute this operation', 403);
		}

		return $next($request);
	}
}
