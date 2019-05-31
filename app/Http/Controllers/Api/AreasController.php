<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Areas;
use App\Http\Resources\Areas as AreasResource;

class AreasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
       
        $areas = Areas::get();
        
        
        return AreasResource::collection($areas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $areas = $request->isMethod('put') ? Areas::findOrFail($request->id) : new Areas;

        $areas->id = $request->input('id');
        $areas->name = $request->input('name');
    
        if($areas->save()) {
            return new AreasResource($areas);
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
        $areas = Areas::findOrFail($id);
        return new AreasResource($areas);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $areas = Areas::findOrFail($id);

        if($areas->delete()) {
            return new AreasResource($areas);
        }
    }
}
