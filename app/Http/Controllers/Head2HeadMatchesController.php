<?php

namespace App\Http\Controllers;

use App\Models\Head2HeadMatch;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Head2HeadMatchesController extends Controller
{
    
    public function createH2HMatch(Request $request){


        $validator = Validator::make($request->all(), [

            'team1_id' => 'required',
            'team2_id'=>'required',
            'date'=>'required',
            'time'=>'required'

        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first()],400);
        }



        $Head2HeadMatch = Head2HeadMatch::create([


            'team1_id' =>$request->input('team1_id'),
            'team2_id' =>$request->input('team2_id'),
            'date' =>$request->input('date'),
            'time' =>$request->input('time'),
            'status' =>"pending_acceptance", 
            
            

        ]);


        return response()->json([

            'code'=>200,
            'message' => 'invite sent successfully',
        
        ]);



    }




    public function getTeamH2HMatch(string $id)
    {
        $team = Team::findOrFail($id);

        $head2HeadMatches = $team->H2HMatch()->get();
    
        if ($head2HeadMatches->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'No head-to-head match found for the team.',
            ], 404);
        }
    
        $formattedMatches = [];

        foreach ($head2HeadMatches as $match) {
            $formattedMatches[] = [
                'id' => $match->id,
                'date' => $match->date,
                'time' => $match->time,
                'location' => $match->location,
                'stad' => $match->stad,
                'status' => $match->status,
                'ibanNumber1' => $match->ibanNumber1,
                'ibanNumber2' => $match->ibanNumber2,
                'team1' => [
                    'id' => $match->team1->id,
                    'teamName' => $match->team1->name,
                    'imagePath' => $match->team1->image ? asset('/storage/' . $match->team1->image->path) : null,
                ],
                'team2' => [
                    'id' => $match->team2->id,
                    'teamName' => $match->team2->name,
                    'imagePath' => $match->team2->image ? asset('/storage/' . $match->team2->image->path) : null,
                ],
            ];
        }

        return response()->json([
            'code' => 200,
            'head2HeadMatches' => $formattedMatches,
        ]);
    }
    



    public function getAllH2HMatches()
    {
        $head2HeadMatches = Head2HeadMatch::with(['team1', 'team2'])->get();

        $formattedMatches = [];

        foreach ($head2HeadMatches as $match) {
            $formattedMatches[] = [

                
                'id' => $match->id,
                'date' => $match->date,
                'time' => $match->time,
                'location' => $match->location,
                'stad' => $match->stad,
                'status' => $match->status,
            
                'team1' => [
                    'id' => $match->team1->id,
                    'teamName' => $match->team1->name,
                    'imagePath' => $match->team1->image ? asset('/storage/' . $match->team1->image->path) : null,
                ],
                'team2' => [
                    'id' => $match->team2->id,
                    'teamName' => $match->team2->name,
                    'imagePath' => $match->team2->image ? asset('/storage/' . $match->team2->image->path) : null,
                ],
            ];
        }

        return response()->json([
            'code' => 200,
            'head2HeadMatches' => $formattedMatches,
        ]);
    }



    public function acceptH2HMatch(String $id){

        $Head2HeadMatch=Head2HeadMatch::findOrFail($id);

        $Head2HeadMatch->update([

            
            'status' =>"pending_payment",
        
            
        ]);


        return response()->json([

            'code'=>200,
            'message' => 'invite approved successfully',
        
        ]);



    }


    public function rejectH2HMatch(String $id){

        $Head2HeadMatch=Head2HeadMatch::findOrFail($id);

        $Head2HeadMatch->delete();


        return response()->json([

            'code'=>200,
            'message' => 'invite rejected successfully',
        
        ]);



    }



    public function selectPaymentMethod(Request $request,String $id){


        $Head2HeadMatch=Head2HeadMatch::findOrFail($id);


        if($Head2HeadMatch->team1_id==$request->input('team_id')){

            $Head2HeadMatch->update([

            
                'ibanNumber1' =>$request->input('ibanNumber'),
            

            ]);
        }

        elseif($Head2HeadMatch->team2_id==$request->input('team_id')){


            $Head2HeadMatch->update([


                'ibanNumber2' =>$request->input('ibanNumber'),
        
            
            ]);

        }


        (new Head2HeadRequestsController)->submitH2Hrequest($id);



        return response()->json([

            'code'=>200,
            'message' => 'payment submited successfully',
        
        ]);



    }

}
