<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TypesToWorks;
use App\Http\Resources\TypesToWorks as TypesToWorksResource;

class TypesToWorksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
        $types = TypesToWorks::get(); 
        
        return TypesToWorksResource::collection($types);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $types = $request->isMethod('put') ? TypesToWorks::findOrFail($request->id) : new TypesToWorks;

        $types->id = $request->input('id');
        $types->title = $request->input('title');
    
        if($types->save()) {
            return new TypesToWorksResource($types);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $types = TypesToWorks::findOrFail($id);
        return new TypesToWorksResource($types);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $types = TypesToWorks::findOrFail($id);

        if($types->delete()) {
            return new TypesToWorksResource($types);
        }
    }
}
