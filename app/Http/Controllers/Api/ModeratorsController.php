<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Moderators;
use App\Http\Resources\Moderators as ModeratorsResource;
use App\User;

class ModeratorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
 
        $moderators = Moderators::with('users', 'cities')->get();
    
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
        $users = $request->isMethod('put') ? User::findOrFail($request->id) : new User;

        $users->id = $request->input('id');
        $users->name = $request->input('name');
        $users->email = $request->input('email');
        $users->phone = $request->input('phone');
        $users->login = $request->input('login');
        $users->role = 'moderator';
        
        if ($request->isMethod('post')) {
            $users->password = bcrypt($request->input('password'));
            $token = $users->createToken('Laravel Password Grant Client')->accessToken;
        }
    
        if($users->save()) { 
            $moderators = $request->isMethod('put') ? Moderators::findOrFail($request->id) : new Moderators;
            $moderators->id = $request->input('id');
            $moderators->users_id = $users->id;
            $moderators->city_id = $request->input('city_id');
        }
     
        if($moderators->save()) {
            return new ManagersResource($moderators);
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
