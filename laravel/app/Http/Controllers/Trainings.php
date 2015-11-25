<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Training;

class Trainings extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function affiliates()
    {
        $trainings = Training::where('type', 'affiliates')->get();
		return response()->ok($trainings);
    }

	public function certification()
	{
		$trainings = Training::where('type', 'certification')->get();
		return response()->ok($trainings);
	}

	public function pro()
	{
		$trainings = Training::where('type', 'pro')->get();
		return response()->ok($trainings);
	}
}
