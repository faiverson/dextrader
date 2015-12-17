<?php

namespace App\Http\Middleware;

use Closure;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN']);
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Max-Age', '1000');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With, Application');

        return $response;
    }
}
