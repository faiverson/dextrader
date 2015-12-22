<?php

namespace App\Http\Middleware;

use Closure;
use Token;
use Page;
/**
 * Class RSA
 *
 * This class is in charge to check if an external page that
 * it is requesting info to the API have access
 *
 * @package App\Http\Middleware
 */
class RSA
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, \Closure $next, $perm)
	{
		$pageId = Token::getPage($request);
		if(empty($pageId)) {
			return response()->error('Unauthorized', 401);
		}

		$page = Page::find($pageId);
		if(empty($page)) {
			return response()->error('Unauthorized', 401);
		}

		$access = explode(',', $page->access);

		if(!in_array($perm, $access)) {
			return response()->error('Unauthorized. Permission denied', 401);
		}

		return $next($request);
	}
}
