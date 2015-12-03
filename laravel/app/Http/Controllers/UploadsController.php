<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Files;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class UploadsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Files::savePublic($request)){
            $file = $request->file('file');
            $filename = $file->getClientOriginalName();

            return response()->ok(array(
                "filename" => $filename
            ));
        }else{
            return response()->error("There was an error trying to save the file");
        }
    }
}