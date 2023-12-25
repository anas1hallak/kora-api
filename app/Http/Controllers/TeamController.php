<?php

namespace App\Http\Controllers;

use App\Models\Team;

use Illuminate\Http\Request;

use App\Models\User;

use Illuminate\Support\Facades\Validator;



class TeamController extends Controller
{
  

    public function createTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teamName' => 'required|unique:teams',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        $user=User::findOrFail($request->input('userId'));

        $team = Team::create([

            'teamName' => $request->input('teamName'),
            'points' =>0,
            'wins' =>0,
            'termsAndConditions'=>"No terms and conditions",
            'coachName'=>$user->fullName,
            'user_id'=>$user->id


        ]);

        $user->selected="selected";
        $user->team_id=$team->id;
        $user->role_id=1;

        $user->update();


        
        return response()->json([

            'code'=>200,
            'message' => 'Team created successfully',
            'team'=>$team,
        
        ]);

        





    }



    public function getTeam(Request $request)
    {


        $team=Team::findOrFail($request->input('teamId'));
        $team->players;

        return response()->json([

            'code'=>200,
            'team'=>$team,
        
        ]);



    }



    public function getAllTeams()
    {


        $teams = Team::with('players')->get();

        return response()->json([

            'code'=>200,
            'teams'=>$teams,
        
        ]);



    }



}
