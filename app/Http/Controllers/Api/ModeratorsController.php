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
        if(json_decode($request->city)) {
            $arr = [];
            foreach (json_decode($request->city) as $key => $value) {
                $arr[] = $value->city_id;
            }
            $moderators = Moderators::with('users', 'addresses')->get();
        }

        if(!json_decode($request->city)) {
            $moderators = Moderators::with('users', 'addresses')->get();  
        }
    
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
        $users->role = 'moderator';
        
        if(!empty($request->input('password'))) {
            $users->password = bcrypt($request->input('password'));
            $token = $users->createToken('Laravel Password Grant Client')->accessToken;
        }
    
        if($users->save()) { 
            $moderator = $request->isMethod('put') ? Moderators::findOrFail($request->id) : new Moderators;
            if($request->isMethod('post')) {
                $moderator->id = $request->input('id');
            }
            $moderator->users_id = $users->id;
        }
     
        if($moderator->save()) {
            if(count($request->address) != 0) {
                $arr = [];
                foreach ($request->address as $key => $value) {
                    
                    if(!isset($value['moderator'])) {
                        $newModeratorAddress = new ModeratorAddresses;
                        $newModeratorAddress->moderator_id = $moderator->id;
                        $newModeratorAddress->city_id = $value['id'];
                        $newModeratorAddress->save();
                    }

                    $arr[] = $value['id'];
                }
                $moderatorAddress = ModeratorAddresses::where('moderator_id', $moderator->id)->whereNotIn('city_id', $arr);
                $moderatorAddress->delete();
            }
            
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
