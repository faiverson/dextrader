<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Role;
use Permission;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class RolesController extends Controller
{
    /**
     * Display a listing of roles
     *
     */
    public function index()
    {
		$roles = Role::all();
		return response()->ok($roles);
	}

	/**
	 * Display a listing of roles with the permissions
	 *
	 */
	public function withPermissions()
	{
		$roles = Role::with('permissions')->get();
//		dd($roles->toArray());
		return response()->ok($roles);
	}
}
