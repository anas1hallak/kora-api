<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use App\Models\Image;

use App\Models\TeamRequests;

use App\Models\FcmToken;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

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
            'age'=>'required',
            'nationality'=>'required',
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
            'age' =>$request->input('age'),
            'nationality'=>$request->input('nationality'),
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
    


    public function profile (string $id){

        $user=User::findOrFail($id);
        $user->image;
        $user->team;

        return response()->json([
            'code' => 200,
            'message' => 'User retreved successfully',
            'user' => $user,
        ]);
        

    }


    public function completeSignup(Request $request){

        $user=User::findOrFail($request->input('user_id'));

        $user->update([

            'playerNumber'=>$request->input('playerNumber'),
            'placeOfPlayer'=>$request->input('placeOfPlayer'),

        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = date('His') . $file->getClientOriginalName();
            $path = $file->storeAs('images', $fileName, 'public');
            
            // Delete previous image, if any
            if ($user->image) {
                Storage::disk('public')->delete($user->image->path);
                $user->image->delete();
            }
    
            // Create a new image model
            $imageModel = new Image;
            $imageModel->path = $path;
            $user->image()->save($imageModel);
        }
    
        $user->load('image');
    
        return response()->json([
            'code' => 200,
            'message' => 'User updated successfully',
            'user' => $user,
        ]);




    }


    public function updateUser(Request $request, $id){

    $validator = Validator::make($request->all(), [

            'fullName' => 'required',
            'phoneNumber' => 'required',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users',
            'age'=>'required',
            'nationality'=>'required',
            'playerNumber' => 'nullable',
            'placeOfPlayer' => 'nullable',
            
            
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 401);
    }

    $user = User::find($id);

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    
    $user->update([

        'fullName' => $request->input('fullName'),
        'phoneNumber' => $request->input('phoneNumber'),
        'password' => $request->has('password') ? bcrypt($request->input('password')) : $user->password,
        'email' => $request->input('email'),
        'age' =>$request->input('age'),
        'nationality'=>$request->input('nationality'),
        'playerNumber' => $request->input('playerNumber'),
        'placeOfPlayer' => $request->input('placeOfPlayer'),
        
    ]);


    

    // Handle image upload/update
    if ($request->hasFile('image')) {
        $file = $request->file('image');
        $fileName = date('His') . $file->getClientOriginalName();
        $path = $file->storeAs('images', $fileName, 'public');
        
        // Delete previous image, if any
        if ($user->image) {
            Storage::disk('public')->delete($user->image->path);
            $user->image->delete();
        }

        // Create a new image model
        $imageModel = new Image;
        $imageModel->path = $path;
        $user->image()->save($imageModel);
    }

    $user->load('image');

    return response()->json([
        'code' => 200,
        'message' => 'User updated successfully',
        'user' => $user,
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





    public function getAllUsers()
    {
        $perPage = request()->input('per_page', 10);

        $users = User::with('team')->paginate($perPage);

        return response()->json([
            'code' => 200,
            'data' => [
                'users' => $users->items(),
                'pagination' => [
                    'total' => $users->total(),
                    'per_page' => $users->perPage(),
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
            ],
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

            /** @var \App\Models\User $user **/

            $user = Auth::user();
            $user->fcmTokens;
            $user->image;
            $user->team;

            $token = $user->createToken('AuthToken')->plainTextToken;



        // Append the image URL to the user data
            $user->image_url = $user->image ? asset('/storage/'. $user->image->path) : null;

        // Remove the 'image' relationship from the response
             unset($user['image']);
             
            
            return response()->json([

                'code'=>200,
                'message' => 'User loged in succesfully',
                'user' => $user,
                'token' => $token,

               
              
            ]);
        } 
        
        else {
            return response()->json([

                'code'=>401,
                'message' => 'Invalid credentials'

            ]);
        }

    }







    public function deleteUser(string $id){
        
        $user = User::findOrFail($id);
    
        if ($user->image) {
            Storage::disk('public')->delete($user->image->path);
            $user->image->delete();
        }
    
        $user->delete();
    
        return response()->json([
            'code' => 200,
            'message' => 'User deleted successfully',
        ]);

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
