<?php

namespace App\Http\Controllers;

use App\Models\Iban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class IbanController extends Controller
{



    public function addIban(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'ibanNumber' => 'required|regex:/^[A-Z]{2}[0-9]{2}[A-Z0-9]{1,30}$/',
          
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }


        $iban = Iban::create([

            'ibanNumber' => $request->input('ibanNumber'),
        

        ]);

        

        return response()->json([

            'code'=>200,
            'message' => 'iban created successfully',
            
        
        ]);
    }




    public function getAllIbans()
    {

        $ibans=Iban::all();

        return response()->json([

            'code'=>200,
            'ibans'=>$ibans,
        
        ]);



    }


    public function deleteIban(string $id){

        $iban=Iban::findOrFail($id);
        $iban->delete();

        return response()->json([

            'code'=>200,
            'message' => 'iban deleted successfully',
            
        
        ]);

    }

   
}
