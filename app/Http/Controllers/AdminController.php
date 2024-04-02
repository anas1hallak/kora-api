<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'userName' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }

        $credentials = $request->only('userName', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {

            /** @var \App\Models\Admin $admin **/


            $admin = Auth::guard('admin')->user();

            $token = $admin->createToken('admin-token')->plainTextToken;
            return response()->json([
                'admin' => $admin,
                'token' => $token
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }



    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userName' => 'required|unique:admins',
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 200);
        }

        $admin = Admin::create([

            'userName' => $request->input('userName'),
            'password' => bcrypt($request->input('password')),
            

        ]);

        return response()->json([

            'code'=>200,
            'message' => 'Admin registered successfully',
        
        ]);

    }



    public function getAll()
    {

        $admins = Admin::all();

        return response()->json([

            'code'=>200,
            'admins' => $admins,
        
        ]);

    }



    public function update(Request $request, String $id)
    {
        $admin = Admin::findOrFail($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'userName' => 'required|unique:admins,userName,' . $id,
            'password' => 'required|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $admin->update([

            'userName' => $request->input('fullName'),
            'password' => bcrypt($request->input('password')),
            

        ]);


        return response()->json(['message' => 'Admin updated successfully'], 200);
    
    }



    public function delete(String $id)
    {
        $admin = Admin::findOrFail($id);

        if (!$admin) {
            return response()->json(['message' => 'Admin not found'], 404);
        }

        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully'], 200);
    }



    public function logout()
    {
        if (Auth::guard('admin')->check()) {
            /** @var \App\Models\Admin $admin **/

            $admin = Auth::guard('admin')->user();
            $admin->tokens()->delete();
            
            return response()->json([
                'code' => 200,
                'message' => 'Successfully logged out',
            ]);
        } else {
            return response()->json([
                'code' => 401,
                'message' => 'Not logged in',
            ], 401);
        }
    }
    
}
