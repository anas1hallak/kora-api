<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Team;
use Illuminate\Http\Request;

use App\Models\TeamRequests;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class TeamRequestsController extends Controller
{



    public function getAllTeamRequests() {

        $user = User::find(Auth::id());
        $team = $user->team;

        $TeamRequests = TeamRequests::where('team_id', $team->id)->get();
    

        foreach ($TeamRequests as $request) {

            $user = $request->user;
                        
            $imagePath = $user ? asset('/storage/' . optional($user->image)->path) : null;

            $request->imagePath=$imagePath;

            unset($request->user);
        }


        return response()->json([
            'status' => 200,
            'TeamRequests' => $TeamRequests,
        ]);
    }


    public function requestToJoinTeam(Request $request){

        $user = Auth::user();
    
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }



        $existingRequest = TeamRequests::where('team_id', $request->input('team_id'))
        ->where('user_id', $user->id)
        ->first();

        if ($existingRequest) {
            return response()->json([
                'code' => 400,
                'message' => 'Request already sent to the team',
            ],200);
        }

        

        $TeamRequests = TeamRequests::create([


            'team_id' => $request->input('team_id'),
            'fullName' =>$user->fullName,
            'nationality' =>$user->nationality,
            'placeOfPlayer' =>$user->placeOfPlayer,
            'user_id' =>$user->id,
            
        ]);

        $team = Team::findOrFail($request->input('team_id'));
        $tokens = User::findOrFail($team->user_id)->fcmTokens()->pluck('fcmToken')->toArray();
        $title='New Join Request';
        $body = 'You have received a join request from '.$user->fullName.', Review and respond to the request to add them to your team';
        (new PushNotificationController)->sendNotification($tokens,$body,$title);


        return response()->json([

            'code'=>200,
            'message' => 'Request sent successfully',
        
        ]);


        
    }


    public function addUserToTeam(string $id){

        $teamRequest=TeamRequests::findOrFail($id);

        $user=User::findOrFail($teamRequest->user_id);


        $user->selected='selected';
        $user->team_id=$teamRequest->team_id;

        $user->update();


        $formation = Formation::create([

            'team_id'=>$teamRequest->team_id,
            'user_id'=>$user->id,
            'position'=>'none',
            'fullName'=>$user->fullName,
            'imagePath' => $user->image ? asset('/storage/'. $user->image->path) : null,


        ]);


        $teamRequest=TeamRequests::findOrFail($id);

        $teamRequest->delete();



        $team = Team::findOrFail($user->team_id);
        $tokens = $user->fcmTokens()->pluck('fcmToken')->toArray();
        $title='Team Acceptance';
        $body = 'Congratulations! You have been accepted into '.$team->teamName.' Get ready to showcase your skills and contribute to the success of the team. Welcome aboard!';
        (new PushNotificationController)->sendNotification($tokens,$body,$title);


        return response()->json([

            'code'=>200,
            'message' => 'user aded to team successfully',
        
        ]);



    }

    public function rejectUserRequest(string $id){


        $teamRequest=TeamRequests::findOrFail($id);

        $teamRequest->delete();


        return response()->json([

            'code'=>200,
            'message' => 'Request deleted successfully',
        
        ]);


    }


    
}
