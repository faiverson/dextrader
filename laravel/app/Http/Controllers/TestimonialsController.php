<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Gateways\TestimonialsGateway;

class TestimonialsController extends Controller
{

    protected $gateway;

    public function __construct(TestimonialsGateway $gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * Show array of testimonials
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $this->gateway->all();
        return response()->ok($query);
    }

    /**
     * Show a user card
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $id = $request->id;

        return response()->ok($this->gateway->findById($id));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->all();
        $response = $this->gateway->create($fields);
        if (!$response) {
            return response()->error($this->gateway->errors()->all());
        }
        return response()->ok($response);
    }

    /**
     * Edit the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->id;
        $fields = $request->all();
        $testimonial = $this->gateway->update($fields, $id);
        if(!$testimonial) {
            return response()->error($this->gateway->errors()->all());
        }
        return response()->ok();
    }
}