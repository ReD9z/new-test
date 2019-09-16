<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function register (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

        $request['password'] = bcrypt($request->password);
        $user = User::create($request->toArray());

        $token = $user->createToken('Laravel Password Grant Client')->accessToken;
        $response = ['token' => $token];

        return response($response, 200);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'errors' => 'errors'
            ], 401);

        $user = User::with('managers', 'moderators', 'managers.cities', 'installers', 'clients')->where('email', $request->email)->first();
        // $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        if ($request->remember_me) 
            $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'user' => $user,
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
        $request->user()->token()->delete();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
}
