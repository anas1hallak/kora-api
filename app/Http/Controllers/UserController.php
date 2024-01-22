<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;

use App\Models\Image;

use App\Models\TeamRequests;

use App\Models\FcmToken;
use App\Models\Formation;
use App\Models\PersonalAccessToken;
use App\Models\Team;
use App\Models\UserRequests;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Validator;

use Laravel\Sanctum\RevokesTokens;
use Laravel\Sanctum\HasApiTokens;


class UserController extends Controller
{


    
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'fullName' => 'required',
            'phoneNumber' => 'required|unique:users',
            'password' => 'required|min:8',
            'email' => 'required|email|unique:users',
            'age'=>'required',
            'nationality'=>'required',
            'fcmToken'=>'required',
          
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first()],400);
        }


        $user = User::create([

            'fullName' => $request->input('fullName'),
            'phoneNumber' => $request->input('phoneNumber'),
            'password' => bcrypt($request->input('password')),
            'email'=>$request->input('email'),
            'age' =>$request->input('age'),
            'nationality'=>$request->input('nationality'),
            'selected'=>'not selected',
            'role_id'=>0,
            'elo'=>"000",


        ]);


    
        $token = $user->createToken('AuthToken')->plainTextToken;

        
        
        FcmToken::create([
            'user_id' => $user->id,
            'fcmToken' => $request->input('fcmToken'),
        ]);

        return response()->json([

            'code'=>200,
            'message' => 'User registered successfully',
            'user'=>$user,
            'token'=>$token
        
        ]);
    }
    





    public function completeSignup(Request $request){

        $user = User::find(Auth::id());

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


    public function getUser(string $id)
    {
        $user = User::with('team')->findOrFail($id);
        $imagePath = $user->image ? asset('/storage/' . $user->image->path) : null;
        $user->imagePath = $imagePath;

        $team=$user->team;

        if($team){

        $imagePath = $team->image ? asset('/storage/'. $team->image->path) : null;
        $team->imagePath = $imagePath;
        unset($team['image']);

        }

        unset($user['image']);

        return response()->json([
            'code' => 200,
            'user' => $user,
        ]);
    }


    public function profile()
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
        
        $imagePath = $user->image ? asset('/storage/' . $user->image->path) : null;
        $user->imagePath = $imagePath;

        $team=$user->team;
        
        if($team){

            $imagePath = $team->image ? asset('/storage/'. $team->image->path) : null;
            $team->imagePath = $imagePath;
            unset($team['image']);
    
            }

        unset($user['image']);


        return response()->json([
            'code' => 200,
            'message' => 'User retrieved successfully',
            'user' => $user,
        ]);
    }



    public function editProfile(Request $request){

        $validator = Validator::make($request->all(), [

                'fullName' => 'required',
                'phoneNumber' => 'required',
                'password' => 'min:8',
                'email' => 'required|email|unique:users,email,' . Auth::id(), 
                'age'=>'required',
                'nationality'=>'required',
                'playerNumber' => 'nullable',
                'placeOfPlayer' => 'nullable',
                
                
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first()],400);
        }

        $user = User::find(Auth::id());
        
            if (!$user) {
                return response()->json([
                    'code' => 401,
                    'message' => 'Unauthorized',
                ], 401);
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


        if($user->role_id===1){

            $team=$user->team;

            $team->update([

                'coachName'=>$user->fullName,
                'coachPhoneNumber'=>$user->phoneNumber,
                'coachEmail'=>$user->email,
                
            ]);


        }


        

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




    public function getAllUsers(Request $request)
    {
        $perPage = request()->input('per_page', 10);
        $query = $request->query('search');

        $usersQuery = User::with(['team', 'image']);

        if($query){

            $usersQuery->where('fullName', 'LIKE', "%$query%");
    
        }

        

            $users = $usersQuery->paginate($perPage);
        


            foreach($users as $user) {

                $imagePath = $user->image ? asset('/storage/' . $user->image->path) : null;
                $user->imagePath = $imagePath;
                unset($user['image']);
            };
        
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
            return response()->json(['message'=>$validator->errors()->first()],400);
        }
    
        $credentials = $request->only('phoneNumber', 'password');
    
        if (Auth::attempt($credentials)) {

            /** @var \App\Models\User $user **/

            $user = Auth::user();

            FcmToken::updateOrCreate(
                ['user_id' => $user->id],
                ['fcmToken' => $request->input('fcmToken')]
            );

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

        /** @var \App\Models\User $user **/

        $user = Auth::user();
        $user->tokens()->delete();
        $user->fcmTokens()->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Successfully logged out',
            
        ]);
    }


    public function editUserSkills(Request $request,string $id){

        $user=User::findOrFail($id);
        $user->update([

            'elo' => $request->input('skills'),
        
            
        ]);

        return response()->json([

            'code'=>200,
            'message' => 'User Skills updated successfully'

        ]);


    }



}
