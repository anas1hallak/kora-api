<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use App\Models\FcmToken;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
<<<<<<< HEAD
=======



    
>>>>>>> 76bbd4a765d3a181c551f9330166de157737eb51
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullName' => 'required',
            'phoneNumber' => 'required',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users',
            'playerNumber' => 'nullable',
            'placeOfPlayer' => 'nullable',
            'fcmToken'=>'required',
          
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
            'selected'=>'not selected',
            'elo'=>"000",


        ]);

        FcmToken::create([
            'user_id' => $user->id,
            'fcmToken' => $request->input('fcmToken'),
        ]);

        return response()->json([

            'code'=>200,
            'message' => 'User registered successfully',
            'user'=>$user,
        
        ]);
    }
    






    public function login(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [

<<<<<<< HEAD
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
          //  $token = $user->createToken('MyApp')->accessToken;

         //=   return response()->json(['token' => $token]);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 400);
=======
            'phoneNumber' => 'required',
            'password' => 'required',
            'fcmToken' => 'required', // Assuming you send FCM token in the request
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
>>>>>>> 76bbd4a765d3a181c551f9330166de157737eb51
        }
    
        $credentials = $request->only('phoneNumber', 'password');
    
        if (Auth::attempt($credentials)) {

            $user = Auth::user();
            $user->fcmTokens;
            
            
            return response()->json([

                'code'=>200,
                'message' => 'User loged in succesfully',
                'user' => $user,
               
              
            ]);
        } 
        
        else {
            return response()->json([

                'code'=>401,
                'message' => 'Invalid credentials'

            ]);
        }

    }



    public function logout()
    {
        Auth::logout();
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }



}
