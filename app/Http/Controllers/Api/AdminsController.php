<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admins;
use App\Http\Resources\Admins as AdminsResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\User;

class AdminsController extends Controller
{
      /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $admin = Admins::with('users')->get();
    
        return AdminsResource::collection($admin);
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
     
        
        
        $users->name = $request->input('name');
        $users->email = $request->input('email');
        $users->phone = $request->input('phone');
        $users->login = $request->input('login');
        $users->role = 'admin';
        
        $this->validate($request, [
            'email' => 'unique:users',
            'email' => Rule::unique('users')->ignore($request->users_id),
        ]);
        
        if ($request->isMethod('post')) {
            $users->password = bcrypt($request->input('password'));
            $token = $users->createToken('Laravel Password Grant Client')->accessToken;
        }
    
        if($users->save()) { 
            $admin = $request->isMethod('put') ? Admins::findOrFail($request->id) : new Admins;

            if($request->isMethod('post')) {
                $admin->id = $request->input('id');
            }
            $admin->users_id = $users->id;

            if($admin->save()) {
                return new AdminsResource($admin);
            }
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
        $admin = Admins::findOrFail($id);
        return new AdminsResource($admin);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $admin = Admins::findOrFail($id);

        if($admin->delete()) {
            return new AdminsResource($admin);
        }
    }
}
