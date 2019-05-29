<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Roles;
use App\Http\Resources\Roles as RolesResource;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $roles = Roles::where('title', 'like', '%' . $request->search . '%')->paginate(6); 
            //TODO сделать метод в модели с несколькими параметрами find('title', 'text' ..).paginate(4)
        } else {
            $roles = Roles::orderBy($request->sortTable, $request->sort)->paginate(6);
        }
        
        return RolesResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $roles = $request->isMethod('put') ? Roles::findOrFail($request->id) : new Roles;

        $roles->id = $request->input('id');
        $roles->title = $request->input('title');
    
        if($roles->save()) {
            return new RolesResource($roles);
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
        $roles = Roles::findOrFail($id);
        return new RolesResource($roles);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $roles = Roles::findOrFail($id);

        if($roles->delete()) {
            return new RolesResource($roles);
        }
    }
}
