<?php

namespace App\Http\Controllers;

use App\Models\Head2HeadMatch;
use App\Models\Head2HeadMatchEvent;
use App\Models\Team;
use App\Models\User;
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


        $teamId = $request->input('team1_id');
        $existingMatch = Head2HeadMatch::where(function ($query) use ($teamId) {
            $query->where('team1_id', $teamId)
                  ->orWhere('team2_id', $teamId);
        })->whereNotIn('status', ['ended'])->exists();
    
        if ($existingMatch) {
            return response()->json([
                'code' => 400,
                'message' => 'Team already has an ongoing or pending head-to-head match.'
            ], 200);
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
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        $team = Team::findOrFail($user->team_id);
        
        if(!$team){

            return response()->json([
                'code' => 404,
                'message' => 'No team for this player yet',
            ],200);


        }

        $teamId=$team->id;

        $head2HeadMatches = Head2HeadMatch::with(['team1', 'team2'])
        ->where(function ($query) use ($teamId) {
            $query->where('team1_id', $teamId)
                ->orWhere('team2_id', $teamId);
        })
        ->whereNotIn('status', ['ended'])
        ->get();
    


        if ($head2HeadMatches->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'No head-to-head match found for the team.',
            ], 404);
        }
    
        $formattedMatches = [];

        foreach ($head2HeadMatches as $match) {

            if (!$match->team1 || !$match->team2) {
                continue;
            }

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

            if (!$match->team1 || !$match->team2) {
                continue;
            }

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







    public function getH2HMatchDetails(string $id)
    {
        $head2HeadMatch = Head2HeadMatch::with(['team1', 'team2'])
            ->where('status', 'approved')
            ->find($id);
    
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
    
        $formattedMatch = [
            'id' => $head2HeadMatch->id,
            'date' => $head2HeadMatch->date,
            'time' => $head2HeadMatch->time,
            'location' => $head2HeadMatch->location,
            'stad' => $head2HeadMatch->stad,
            'winner' => $head2HeadMatch->winner,
            'goals1' => $head2HeadMatch->goals1,
            'goals2' => $head2HeadMatch->goals2,
            'status' => $head2HeadMatch->status,
            'team1' => [
                'id' => $head2HeadMatch->team1->id,
                'teamName' => $head2HeadMatch->team1->teamName,
                'imagePath' => $head2HeadMatch->team1->image ? asset('/storage/' . $head2HeadMatch->team1->image->path) : null,
            ],
            'team2' => [
                'id' => $head2HeadMatch->team2->id,
                'teamName' => $head2HeadMatch->team2->teamName,
                'imagePath' => $head2HeadMatch->team2->image ? asset('/storage/' . $head2HeadMatch->team2->image->path) : null,
            ],
        ];
    
        return response()->json([
            'code' => 200,
            'head2HeadMatch' => $formattedMatch,
            'events' => $formattedEvents,
        ]);
    }

}
