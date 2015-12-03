<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Files;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FilesController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
		$path = $request->path ? 'images/' . $request->path . '/' : 'images/';
        if(Files::save($request, 'file', false, $path)){
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