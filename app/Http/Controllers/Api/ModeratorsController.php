<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Moderators;
use App\Models\ModeratorAddresses;
use App\Http\Resources\Moderators as ModeratorsResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
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
        $moderators = Moderators::with('users', 'cities', 'addresses.city')->get();
    
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
        $users = $request->isMethod('put') ? User::findOrFail($request->users_id) : new User;

        // if($request->isMethod('post')) {
        //     $users->id = $request->input('id');
        // }
        
        // $this->validate($request, [
        //     'email' => 'unique:users',
        //     'email' => Rule::unique('users')->ignore($request->users_id),
        // ]);

        // $users->name = $request->input('name');
        // $users->email = $request->input('email');
        // $users->phone = $request->input('phone');
        // $users->login = $request->input('login');
        // $users->role = 'moderator';
        
        // if(!empty($request->input('password'))) {
        //     $users->password = bcrypt($request->input('password'));
        //     $token = $users->createToken('Laravel Password Grant Client')->accessToken;
        // }
    
        // if($users->save()) { 
            $moderator = $request->isMethod('put') ? Moderators::findOrFail($request->id) : new Moderators;
            // if($request->isMethod('post')) {
            //     $moderator->id = $request->input('id');
            // }
            // $moderator->users_id = $users->id;
        // }
     
        if($moderator->save()) {
            $moderatorAddress = ModeratorAddresses::where('moderator_id', $moderator->id)->get();
            dd($moderatorAddress);
            // $moderatorAddress = ;
            
            // $entrances = Entrances::where([
            //     ['address_id', $id], 
            //     ['file_id', '!=', null]
            // ])->pluck('file_id')->all();
            // if($request->address) {
            //     ModeratorAddresses::where('moderator_id', $id)->get();
            //     foreach ($address as $key => $value) {
            //         // $address = ModeratorAddresses::where('moderator_id', $id)->get();
            //     }
            //     // foreach ($variable as $key => $value) {
            //     //     # code...
            //     // }
            // }
            return new ModeratorsResource($moderator);
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
