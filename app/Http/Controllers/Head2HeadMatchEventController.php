<?php

namespace App\Http\Controllers;

use App\Models\Head2HeadMatchEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Head2HeadMatchEventController extends Controller
{


    public function addH2HEvent(Request $request){


        $validator = Validator::make($request->all(), [

            'Head2HeadMatch_id' => 'required',
            'team_id'=>'required',
            'user_id'=>'required',
            'time'=>'required',
            'type'=>'required'


        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first()],400);
        }


        $event = Head2HeadMatchEvent::create([


            'Head2HeadMatch_id' =>$request->input('Head2HeadMatch_id'),
            'team_id' =>$request->input('team_id'),
            'user_id' =>$request->input('user_id'),
            'time' =>$request->input('time'),
            'type' =>$request->input('type'),
            
            

        ]);


        return response()->json([
            'code' => 200,
            'message' => 'event created successfully.',
        ], 200);


    }




    

}
