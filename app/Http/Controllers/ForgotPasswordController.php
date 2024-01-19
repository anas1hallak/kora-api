<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'phoneNumber' => 'required|exists:users,phoneNumber',
            'password' => 'required|min:8',

        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first()],400);
        }

        $phoneNumber = $request->input('phoneNumber');
        $user = User::where('phoneNumber', $phoneNumber)->first();

       
        $user->update([

            'password' => bcrypt($request->input('password')),

        ]);

        return response()->json([
            'code' => 200,
            'message' => 'Password updated successfully',
        ]);
    }


    

    public function checkPhoneNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'phoneNumber' => 'required|exists:users,phoneNumber',

        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first()],404);
        }


        return response()->json([
            'code' => 200,
            'message' => 'Phone number exists',
        ]);
    }
}
