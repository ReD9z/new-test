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
        if ($request->search) {
            $moderators = Moderators::with('users')->where('name', 'like', '%' . $request->search . '%')
            ->orWhere('email', 'like', '%' . $request->search . '%')
            ->paginate(6); 
            //TODO сделать метод в модели с несколькими параметрами find('title', 'text' ..).paginate(4)
        } else {
            $moderators = Moderators::with('users')->paginate(6);
        }
        
        return ModeratorsResource::collection($moderators);
        // return response()->json(['errors' => [], 'data' => $request, 'status' => 200], 200);
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
