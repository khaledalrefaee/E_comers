<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){

        $filds =$request->validate([
            'First_Name'      => 'required|regex:/^[A-Z][a-z]*$/',
            'Last_Name'       => 'required|regex:/^[A-Z][a-z]*$/',
            'phone'           => 'required||regex:/^09\d{8}$/',
            'email'           => 'required|string|unique:Users',
            'password'        =>'required|string'
        ]);
        $user=User::create([
            'First_Name'       =>$filds['First_Name'],
            'Last_Name'        =>$filds['Last_Name'],
            'phone'            =>$filds['phone'],
            'email'            =>$filds['email'],
            'password'         => bcrypt($filds['password'])
        ]);

        return response($user,201);
    }

    public function login(Request $request){
        $filds =$request->validate([
            'email'     => 'required|string',
            'password'  =>'required|string'
        ]);

        //check email
        $user = User::where('email',$filds['email'])->first();
        //check password
        if(!$user ||!Hash::check($filds['password'],$user->password)){
            return response(['mesegag' => 'bad'], 401);
        }

        $token =$user->createToken('myappToken')->plainTextToken;

        $respons=[
            'user'=>$user,
            'token' =>$token
        ];

        return response($respons,201);
    }

    public function logout(Request $request)
    {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function userProfile() {
        return response()->json(auth()->user());
    }
}
