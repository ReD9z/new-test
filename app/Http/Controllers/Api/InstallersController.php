<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Installers;
use App\Http\Resources\Installers as InstallersResource;
use App\User;

class InstallersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $installers = Installers::with('users', 'address')->where('title', 'like', '%' . $request->search . '%')->paginate(6); 
            //TODO сделать метод в модели с несколькими параметрами find('title', 'text' ..).paginate(4)
        } else {
            $installers = Installers::with('users', 'address')->paginate(6);
        }
        
        return InstallersResource::collection($installers);
    }

    public function allInstallers(Request $request)
    {
        $clients = Installers::with('users')->get();
        return InstallersResource::collection($clients);
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
        
        if ($request->isMethod('post')) {
            $users->password = bcrypt($request->input('password'));
            $token = $users->createToken('Laravel Password Grant Client')->accessToken;
        }
    
        if($users->save()) { 
            $installers = $request->isMethod('put') ? Installers::findOrFail($request->id) : new Installers;
            $installers->id = $request->input('id');
            $installers->user_id = $users->id;
            $installers->city_id = $request->input('city_id');
            $installers->moderator_id = $request->input('moderator_id');

            if($installers->save()) {
                return new InstallersResource($installers);
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
        $installers = Installers::findOrFail($id);
        return new InstallersResource($installers);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $installers = Installers::findOrFail($id);

        if($installers->delete()) {
            return new InstallersResource($installers);
        }
    }
}
