<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {

        if($request->has('email')){
            $fields = $request->validate([
                'email' => 'required|string|unique:users,email',
                'name' => 'required|string',
                'password' => 'required|string|confirmed|min:8',
                'is_admin' => 'boolean'
            ]);

            $user = User::create([
                'name' => $fields ['name'],
                'email' => $fields ['email'],
                'password' => bcrypt($fields ['password'])
            ]); 
        }else{
            $fields = $request->validate([
                'phone_number' => 'required|string|unique:users,phone_number',
                'name' => 'required|string',
                'password' => 'required|string|confirmed|min:8',
                'is_admin' => 'boolean'
            ]);

            $user = User::create([
                'name' => $fields ['name'],
                'phone_number' => $fields ['phone_number'],
                'password' => bcrypt($fields ['password'])
            ]); 
        }
        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
    
    public function login(Request $request) {
        if($request->has('email')){
            $fields = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string'
            ]);
        }else{
            $fields = $request->validate([
                'phone_number' => 'required|string',
                'password' => 'required|string'
            ]);
                
        }

        // Check email pr phone number
        if($request->has('email')){
            $user = User::where('email', $fields['email'])->first();
        }else{
            $user = User::where('phone_number', $fields['phone_number'])->first();
        }
        // Check password
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Bad creds'
            ], 401);
        }

        $token = $user->createToken('myapptoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }
}
