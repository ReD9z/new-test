<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Moderators;
use App\Http\Resources\Moderators as ModeratorsResource;

class ModeratorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
 
        $moderators = Moderators::with('users')->get();
    
        return ModeratorsResource::collection($moderators);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $moderators = $request->isMethod('put') ? Moderators::findOrFail($request->id) : new Moderators;

        // $moderators->id = $request->input('id');
        // $moderators->name = $request->input('name');
        
        if($moderators->save()) { 
            return new ModeratorsResource($moderators);
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
        $moderators = Moderators::findOrFail($id);
        return new ModeratorsResource($moderators);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $moderators = Moderators::findOrFail($id);

        if($moderators->delete()) {
            return new ModeratorsResource($moderators);
        }
    }
}
