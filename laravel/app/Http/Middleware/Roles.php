<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;
use Auth;

class Roles
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role)
    {

//        $uRole = (int) Auth::user()->role;
//        if(is_string($role)) {
//            $role = $this->roles[$role];
//        }
//
//        if ($role > $uRole) {
//            if($request->ajax()) {
//                return array('success' => false, 'error' => 'Permission denied');
//            } else {
//                return redirect('/admin');
//            }
//
//        }
//        else {
//            return $next($request);
//        }

    }

}