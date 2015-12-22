<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Country;
use City;

class CountriesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function countries(Request $request)
    {
        $query = $request->q;

        $countries = Country::where('name', 'LIKE', "%$query%")->get();
        return response()->ok($countries);
    }

    public function cities(Request $request)
    {
        $query = $request->q;
        $countryCode = $request->country_code;

        $cities = City::where('name', 'LIKE', "%$query%")->where('country_code', 'LIKE', "%$countryCode%")->get();
        return response()->ok($cities);
    }
}