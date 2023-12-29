<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use App\Models\Image;

use App\Models\TeamRequests;

use App\Models\FcmToken;

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


        
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $fileName = date('His') . $fileName;
            $path = $request->file('image')->storeAs('images', $fileName, 'public');
            $imageModel = new Image;
            $imageModel->path = $path; 
            $user->image()->save($imageModel);
        }
    
        $user->load('image');

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
    




    public function requestToJoinTeam(Request $request){

        $user=User::findOrFail($request->input('user_id'));

        $TeamRequests = TeamRequests::create([


            'team_id' => $request->input('team_id'),
            'message' =>$user->fullName. ' wants to join this team',
            'user_id' =>$request->input('user_id'),
            
        ]);

        return response()->json([

            'code'=>200,
            'message' => 'Request sent successfully',
        
        ]);


        
    }




    public function getAllUsers(){


        $users=User::all();

        return response()->json([

            'code'=>200,
            'users'=>$users,
        
        ]);


    }
    



    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [

            'phoneNumber' => 'required',
            'password' => 'required',
            'fcmToken' => 'required', // Assuming you send FCM token in the request
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
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
