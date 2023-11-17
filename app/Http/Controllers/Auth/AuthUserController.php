<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Sanctum\Sanctum;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthUserController extends Controller
{
    
    public function login(Request $request){
        $request->validate([
            "email"=>"required | email",
            "password"=>"required"
        ]);
        // find the user
        $authAttempt = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
        if(!$authAttempt){            
            return response()->json([
                "message"=>"Email and Password Does not match"
            ]);
        }

        $user = User::where("email",$request->email)->first();

        // token based
        $userToken = $user->createToken(env('SANCTURM_TOKEN_SECRET'))->plainTextToken;
        
        return response()->json([
            "user"=>$user,
            "token"=>$userToken
        ]);
    }

    public function signup(Request $request){
        // validate the incomming request
        $request->validate([
            "name"=>"required",
            "email"=>"required | email | unique:users,email",
            "password"=>"required"
        ]);
        // creat a new user
        $newUser = User::create([
            "name" =>$request->name,
            "email"=>$request->email,
            "password"=>Hash::make($request->password)
        ]);
        // sanctum toke 
        $token  = $newUser->createToken(env("SANCTURM_TOKEN_SECRET"))->plainTextToken;
    
        return response()->json([
            "user"=>$newUser,
            "token"=>$token]);

    }

    public function logout(Request $request){
        $request->user()->tokens()->delete();
        $request->user()->currentAccessToken()->delete();
    }
}
