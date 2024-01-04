<?php

namespace App\Http\Controllers;

use App\Models\Team;

use Illuminate\Http\Request;

use App\Models\User;

use App\Models\ChampionshipRequests;

use App\Models\Teamimage;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;



class TeamController extends Controller
{
  

    public function createTeam(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'teamName' => 'required|unique:teams',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user=User::findOrFail($request->input('user_id'));

        $team = Team::create([

            'teamName' => $request->input('teamName'),
            'points' =>0,
            'wins' =>0,
            'termsAndConditions'=>"No terms and conditions",
            'coachName'=>$user->fullName,
            'coachPhoneNumber'=>$user->phoneNumber,
            'coachEmail'=>$user->email,
            'user_id'=>$user->id


        ]);

        
        

        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $fileName = date('His') . $fileName;
            $path = $request->file('image')->storeAs('images', $fileName, 'public');
            $imageModel = new Teamimage;
            $imageModel->path = $path; 
            $team->image()->save($imageModel);
        }
    
        $team->load('image');

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



    public function getTeam(string $id)
    {


        $team=Team::findOrFail($id);
        $team->players;

        return response()->json([

            'code'=>200,
            'team'=>$team,
        
        ]);



    }

    public function getTeamPlayers(string $id)
    {


        $team=Team::findOrFail($id);
        $players=$team->players;

        return response()->json([

            'code'=>200,
            'players'=>$players,
        
        ]);



    }


    // public function getAllTeams()
    // {


    //     $teams = Team::with('players')->get();

    //     return response()->json([

    //         'code'=>200,
    //         'teams'=>$teams,
        
    //     ]);



    // }





    public function getAllTeams()
    {
        $perPage = request()->input('per_page', 10);
    
        $teams = Team::with('players')->paginate($perPage);
    
        return response()->json([
            'code' => 200,
            'data' => [
                'teams' => $teams->items(),
                'pagination' => [
                    'total' => $teams->total(),
                    'per_page' => $teams->perPage(),
                    'current_page' => $teams->currentPage(),
                    'last_page' => $teams->lastPage(),
                    'from' => $teams->firstItem(),
                    'to' => $teams->lastItem(),
                ],
            ],
        ]);
    }
    




    public function addUserToTeam(Request $request){

        $user=User::findOrFail($request->input('user_id'));
    
        $user->selected='selected';
        $user->team_id=$request->input('team_id');

        $user->update();


        return response()->json([

            'code'=>200,
            'message' => 'user aded to team successfully',
        
        ]);



    }




    public function requestToJoinChampionship(Request $request){

        $team=Team::findOrFail($request->input('team_id'));

        $ChampionshipRequests = ChampionshipRequests::create([


            'team_id' => $request->input('team_id'),
            'message' =>$team->name. ' Team wants to join this championship',
            'championship_id' =>$request->input('championship_id'),
            
        ]);

        return response()->json([

            'code'=>200,
            'message' => 'Request sent successfully',
        
        ]);


        
    }



    public function deleteTeam(string $id){


        $team = Team::findOrFail($id);
        $users = User::where('team_id', $team->id)->get();

        foreach ($users as $user) {

            $user->team_id = null;
            $user->selected = "not selected";
            $user->role_id=0;
            $user->update();
        }


        $team->delete();

        return response()->json([

            'code'=>200,
            'message' => 'Team deleted successfully'

        ]);




    }



}
