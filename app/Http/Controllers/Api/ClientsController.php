<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Http\Resources\Clients as ClientsResource;
use App\User;

class ClientsController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index(Request $request)
    {
        $clients = Clients::with('cities', 'users')->get(); 
         
        return ClientsResource::collection($clients);
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
        $users->role = 'client';
        
        if ($request->isMethod('post')) {
            $users->password = bcrypt($request->input('password'));
            $token = $users->createToken('Laravel Password Grant Client')->accessToken;
        }
    
        if($users->save()) { 
            $clients = $request->isMethod('put') ? Clients::findOrFail($request->id) : new Clients;
            $clients->id = $request->input('id');
            $clients->users_id = $users->id;
            $clients->city_id = $request->input('city_id');
            $clients->legal_name = $request->input('legal_name');
            $clients->actual_title = $request->input('actual_title');
            $clients->legal_address = $request->input('legal_address');
            $clients->actual_address = $request->input('actual_address');
            $clients->bik = $request->input('bik');
            $clients->cor_score = $request->input('cor_score');
            $clients->settlement_account = $request->input('settlement_account');
            $clients->bank_name = $request->input('bank_name');


            if($clients->save()) {
                return new ClientsResource($clients);
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
        $clients = Clients::findOrFail($id);
        return new ClientsResource($clients);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $clients = Clients::findOrFail($id);

        if($clients->delete()) {
            return new ClientsResource($clients);
        }
    }
}
