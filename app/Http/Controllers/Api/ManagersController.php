<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Managers;
use App\Http\Resources\Managers as ManagersResource;

class ManagersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $managers = Managers::get(); 
        
        return ManagersResource::collection($managers);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $managers = $request->isMethod('put') ? Managers::findOrFail($request->id) : new Managers;

        $managers->id = $request->input('id');
        $managers->title = $request->input('title');
    
        if($managers->save()) {
            return new ManagersResource($managers);
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
        $managers = Managers::findOrFail($id);
        return new ManagersResource($managers);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $managers = Managers::findOrFail($id);

        if($managers->delete()) {
            return new ManagersResource($managers);
        }
    }
}
