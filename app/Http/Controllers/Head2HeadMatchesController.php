<?php

namespace App\Http\Controllers;

use App\Models\Head2HeadMatch;
use App\Models\Head2HeadMatchEvent;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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




    public function getTeamH2HMatch()
    {
        $user = Auth::user();
    
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        $team = $user->team;
        
        if(!$team){

            return response()->json([
                'code' => 404,
                'message' => 'No team for this player yet',
            ],200);


        }

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
                'winner' => $match->winner,
                'goals1' => $match->goals1,
                'goals2' => $match->goals2,
                'status' => $match->status,
                'ibanNumber1' => $match->ibanNumber1,
                'ibanNumber2' => $match->ibanNumber2,
                'team1' => [
                    'id' => $match->team1->id,
                    'teamName' => $match->team1->teamName,
                    'imagePath' => $match->team1->image ? asset('/storage/' . $match->team1->image->path) : null,
                ],
                'team2' => [
                    'id' => $match->team2->id,
                    'teamName' => $match->team2->teamName,
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
        
    
        $perPage = 10;

        $head2HeadMatches = Head2HeadMatch::with(['team1', 'team2'])
            ->where('status', 'approved')
            ->paginate($perPage);

        $formattedMatches = [];

        foreach ($head2HeadMatches as $match) {
            $formattedMatches[] = [

                
                'id' => $match->id,
                'date' => $match->date,
                'time' => $match->time,
                'location' => $match->location,
                'stad' => $match->stad,
                'winner' => $match->winner,
                'goals1' => $match->goals1,
                'goals2' => $match->goals2,
                'status' => $match->status,
            
                'team1' => [
                    'id' => $match->team1->id,
                    'teamName' => $match->team1->teamName,
                    'imagePath' => $match->team1->image ? asset('/storage/' . $match->team1->image->path) : null,
                ],
                'team2' => [
                    'id' => $match->team2->id,
                    'teamName' => $match->team2->teamName,
                    'imagePath' => $match->team2->image ? asset('/storage/' . $match->team2->image->path) : null,
                ],
            ];
        }

        // Return the paginated matches
        return response()->json([
            'code' => 200,
            'head2HeadMatches' => $formattedMatches,
            'pagination' => [
                'total' => $head2HeadMatches->total(),
                'per_page' => $head2HeadMatches->perPage(),
                'current_page' => $head2HeadMatches->currentPage(),
                'last_page' => $head2HeadMatches->lastPage(),
                'from' => $head2HeadMatches->firstItem(),
                'to' => $head2HeadMatches->lastItem(),
            ],
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




    public function getH2HMatchEvents(string $id)
    {
        $head2HeadMatch = Head2HeadMatch::find($id);

        if (!$head2HeadMatch) {
            return response()->json([
                'code' => 404,
                'message' => 'Head-to-head match not found.',
            ], 404);
        }
    
        $events = Head2HeadMatchEvent::with('team', 'user')
            ->where('Head2HeadMatch_id', $id)
            ->get();



            $formattedEvents = [];

        foreach ($events as $event) {
            $formattedEvents[] = [

                
                'id' => $event->id,
                'playerName' => $event->user->fullName,
                'teamName' => $event->team->teamName,
                'time' => $event->time,
                'type' => $event->type,
                
                
            ];
        }


    
        return response()->json([
            'code' => 200,
            'events' => $formattedEvents,
        ]);
    }

}
