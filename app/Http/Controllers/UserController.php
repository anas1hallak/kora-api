<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required',
            'phoneNumber' => 'required',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users',
            'playerNumber' => 'nullable',
            'placeOfPlayer' => 'nullable',
          
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }


        $user = User::create([
            'fullName' => $request->input('fullName'),
            'phoneNumber' => $request->input('phoneNumber'),
            'password' => bcrypt($request->input('password')),
            'email'=>$request->input('email'),
            'playerNumber'=>$request->input('palyerNumber'),
            'placeOfPlayer'=>$request->input('placeOfPlayer'),
        ]);

        return response()->json(['message' => 'User registered successfully']);
    }
    






    public function login(Request $request)
    {
        $credentials = $request->only('phoneNumber', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
          //  $token = $user->createToken('MyApp')->accessToken;

         //=   return response()->json(['token' => $token]);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }
    }






    public function protectedRoute()
    {
        return response()->json(['message' => 'This is a protected route']);
    }



}
