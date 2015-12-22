<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Provider;
use Validator;

class ProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $start = $request->input('start') ? $request->input('start') : 0;
        $length = $request->input('length') ? $request->input('length') : 10;
        $order = $request->input('sortBy');
        $dir = $request->input('sortDir');

        $order_by = $order ? array('column' => $order, 'dir' => $dir) : array('column' => 0, 'dir' => 'desc');
        $sort = ['id', 'name', 'min_deposit', 'us_traders', 'web_site'];

        $order_by['column'] = $sort[$order_by['column']];

        $providers = Provider::skip($start)
            ->take($length)
            ->orderBy($order_by['column'], $order_by['dir'])
            ->get();

        $data = array(
            "totalItems" => Provider::all()->count(),
            "items" => $providers
        );

        return response()->ok($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $provider = new Provider();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'image' => 'required',
            'min_deposit' => 'required|numeric',
            'us_traders' => 'required|boolean',
            'review' => 'required',
            'web_site' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->error($validator->errors()->all());
        }
        $provider->name = $request->input('name');
        $provider->image = $request->input('image');
        $provider->min_deposit = $request->input('min_deposit');
        $provider->us_traders = $request->input('us_traders');
        $provider->review = $request->input('review');
        $provider->web_site = $request->input('web_site');

        $provider->save();
        return response()->added();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $provider = Provider::where('id', $id)->first();
        return response()->ok($provider);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->input('id');
        $name = $request->input('name');
        $image = $request->input('image');
        $min_deposit = $request->input('min_deposit');
        $us_traders = $request->input('us_traders');
        $review = $request->input('review');
        $web_site = $request->input('web_site');

        $provider = Provider::find($id);
        $update = [];

        if($name) {
            $update['name'] = $name;
            $validations['name'] = 'required';
        }

        if($image) {
            $update['image'] = $image;
            $validations['image'] = 'required';
        }

        if($min_deposit) {
            $update['min_deposit'] = $min_deposit;
            $validations['min_deposit'] = 'required|numeric';
        }

        if($us_traders) {
            $update['us_traders'] = $us_traders;
            $validations['us_traders'] = 'required|boolean';
        }

        if($review) {
            $update['review'] = $review;
            $validations['review'] = 'required';
        }

        if($web_site) {
            $update['web_site'] = $web_site;
            $validations['web_site'] = 'required';
        }

        if(!empty($validations)) {
            $validator = Validator::make($update, $validations);
            if ($validator->fails()) {
                return response()->error($validator->errors()->all());
            }
        }

        $provider->update($update);
        return response()->ok();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
