<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Role;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    /**
     * Display a listing of roles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
		$roles = Role::all();
		return response()->ok($roles);
	}
}
