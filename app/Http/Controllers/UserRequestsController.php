<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Team;
use App\Models\User;
use App\Models\UserRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;



class UserRequestsController extends Controller
{
    
    public function getAllUserRequests() {

        $user = User::find(Auth::id());
        
        $userRequests = UserRequests::where('user_id', $user->id)->get();
    
        foreach ($userRequests as $request) {

            $team = $request->team;
                        
            $imagePath = $team ? asset('/storage/' . optional($team->image)->path) : null;

            $request->imagePath=$imagePath;

            unset($request->team);
        }
    
        return response()->json([
            'status' => 200,
            'UserRequests' => $userRequests,
        ]);
    }




    public function inviteToMyTeam(Request $request){

        $user = User::find(Auth::id());
    
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $team = $user->team;


        $existingRequest = UserRequests::where('team_id', $team->id)
        ->where('user_id', $request->input('user_id'))
        ->first();

        if ($existingRequest) {
            return response()->json([
                'code' => 400,
                'message' => 'Request already sent to the user',
            ],200);
        }
        
        $userRequest = UserRequests::create([


            'team_id' => $team->id,
            'message' =>'Invitation to join '.$team->teamName,
            'user_id' =>$request->input('user_id'),
            
        ]);
        
        $tokens = $user->fcmTokens()->pluck('fcmToken')->toArray();
        $title='Team Invitation';
        $body ='You have received an invitation to join a team. Check your notifications to accept or decline the invitation. Join the team and start playing today!';
        (new PushNotificationController)->sendNotification($tokens,$body,$title);


        return response()->json([

            'code'=>200,
            'message' => 'Request sent successfully',
        
        ]);


    }

    public function acceptTeamRequest(string $id){

        $userRequest=UserRequests::findOrFail($id);

        $user=User::findOrFail($userRequest->user_id);


        $user->selected='selected';
        $user->team_id=$userRequest->team_id;
        $user->update();


        $formation = Formation::create([

            'team_id'=>$userRequest->team_id,
            'user_id'=>$user->id,
            'position'=>'none',
            'fullName'=>$user->fullName,

        ]);

        UserRequests::where('user_id', $user->id)->delete();


        $team = Team::findOrFail($user->team_id);
        $tokens = User::findOrFail($team->user_id)->fcmTokens()->pluck('fcmToken')->toArray();
        $title='Player Accepted Invitation';
        $body = 'Good news '.$user->fullName.' has accepted your invitation to join the team. Welcome them to the squad!';
        (new PushNotificationController)->sendNotification($tokens,$body,$title);


        return response()->json([

            'code'=>200,
            'message' => 'user aded to team successfully',
        
        ]);



    }


    public function rejectTeamRequest(string $id){


        $userRequest=UserRequests::findOrFail($id);

        $userRequest->delete();


        return response()->json([

            'code'=>200,
            'message' => 'Request deleted successfully',
        
        ]);


    }

}
