<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Managers;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Http\Resources\Managers as ManagersResource;
use Illuminate\Validation\Rule;

class ManagersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $managers = Managers::with('users', 'cities', 'moderator.users')->get();   
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
        $users = $request->isMethod('put') ? User::findOrFail($request->users_id) : new User;

        if($request->isMethod('post')) {
            $users->id = $request->input('id');
        }
        
        $this->validate($request, [
            'email' => 'unique:users',
            'email' => Rule::unique('users')->ignore($request->users_id),
        ]);

        $users->name = $request->input('name');
        $users->email = $request->input('email');
        $users->phone = $request->input('phone');
        $users->login = $request->input('login');
        $users->role = 'manager';

        if ($request->isMethod('post')) {
            $users->password = bcrypt($request->input('password'));
            $token = $users->createToken('Laravel Password Grant Client')->accessToken;
        }
    
        if($users->save()) { 
            $managers = $request->isMethod('put') ? Managers::findOrFail($request->id) : new Managers;
            if($request->isMethod('post')) {
                $managers->id = $request->input('id');
            }
            $managers->id = $request->input('id');
            $managers->users_id = $users->id;
            $managers->city_id = $request->input('city_id');
            $managers->moderator_id = $request->input('moderator_id');

            if($managers->save()) {
                return new ManagersResource($managers);
            }
        }
     
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
