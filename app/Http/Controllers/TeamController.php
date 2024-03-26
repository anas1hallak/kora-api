<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Team;
use App\Models\User;
use App\Models\ChampionshipRequests;
use App\Models\Formation;
use App\Models\Gteam;
use App\Models\Teamimage;
use App\Models\TeamRequests;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;



class TeamController extends Controller
{
  

    public function createTeam(Request $request)
    {


        $user = User::find(Auth::id());
    
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }


        $validator = Validator::make($request->all(), [

            'teamName' => 'required|unique:teams',
            'image'=>'required',
            'termsAndConditions'=>'required'

        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first()],400);
        }

       
        $team = Team::create([

            'teamName' => $request->input('teamName'),
            'points' =>0,
            'wins' =>0,
            'rate'=>0.5,
            'termsAndConditions'=>$request->input('termsAndConditions'),
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
        $user->isCoach='true';
        $user->update();


        
        $formation = Formation::create([

            'team_id'=>$team->id,
            'user_id'=>$user->id,
            'position'=>'none',
            'fullName'=>$user->fullName,
            


        ]);
        
        return response()->json([

            'code'=>200,
            'message' => 'Team created successfully',
            'team'=>$team,
        
        ]);

        

    }



    public function getTeam(string $id)
    {
        $team = Team::findOrFail($id);
        $team->load('players', 'image');

        $teamCount = $team->players()->count();
        $imagePath = $team->image ? asset('/storage/'. $team->image->path) : null;

        $team->teamCount = $teamCount;
        $team->imagePath = $imagePath;


        foreach ($team->players as $player) {

            $player->imagePath = $player->image ? asset('/storage/'. $player->image->path) : null;
            unset($player['image']);
            
        }

        unset($team['image']);


        return response()->json([
            'code' => 200,
            'team' => $team,
        ]);
    }



    public function teamProfile()
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
    
        $team->load('players', 'image');

        $teamCount = $team->players()->count();
        $imagePath = $team->image ? asset('/storage/'. $team->image->path) : null;

        $team->teamCount = $teamCount;
        $team->imagePath = $imagePath;
        
        
         foreach ($team->players as $player) {

            $player->imagePath = $player->image ? asset('/storage/'. $player->image->path) : null;
            unset($player['image']);
            
        }

        unset($team['image']);

    
        return response()->json([
            'code' => 200,
            'message' => 'team profile retrieved successfully',
            'team' => $team,

        ]);
    }








    public function editTeamProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'teamName' => 'required',
            'termsAndConditions'=>'required',

            
        ]);

        if ($validator->fails()) {
            return response()->json(['message'=>$validator->errors()->first()],400);
        }
        
        $user = Auth::user();
    
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        $team = $user->team;

        $team->update([

            'teamName' => $request->input('teamName'),
            'termsAndConditions'=>$request->input('termsAndConditions'),
            
        ]);


        Gteam::where('team_id', $team->id)->update([
            'teamName' => $team->teamName,
        ]);


        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $fileName = date('His') . $file->getClientOriginalName();
            $path = $file->storeAs('images', $fileName, 'public');
            
            // Delete previous image, if any
            if ($team->image) {
                Storage::disk('public')->delete($team->image->path);
                $team->image->delete();
            }

            // Create a new image model
            $imageModel = new Teamimage;
            $imageModel->path = $path;
            $team->image()->save($imageModel);
        }

        $team->load('image');

    
        return response()->json([
            'code' => 200,
            'message' => 'team profile updated successfully',
            'team' => $team,

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




    public function getAllTeams(Request $request)
    {
        $perPage = request()->input('per_page', 10);
        $query = $request->query('search');

        $teamsQuery = Team::with('image');


        if($query){

            $teamsQuery->where('teamName', 'LIKE', "%$query%");

        }

        $teams = $teamsQuery->paginate($perPage);



        $teamdata = [];

            foreach ($teams as $team) {

            
                $imagePath = $team->image ? asset('/storage/'. $team->image->path) : null;
                $teamCount = $team->players()->count();

                
            

            $teamdata[] = [

                'id' => $team->id,
                'teamName' => $team->teamName,
                'points' => $team->points,
                'rate' => $team->rate,
                'wins' => $team->wins,
                'coachName' => $team->coachName,
                'coachPhoneNumber' => $team->coachPhoneNumber,
                'coachEmail' => $team->coachEmail,
                'teamCount'=>$teamCount,
                'imagePath' => $imagePath,

            ];
        }
        

        return response()->json([
            'code' => 200,
            'data' => [
                'teams' =>$teamdata,
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

    




   

    public function requestToJoinChampionship(Request $request){

        $user = Auth::user();
    
        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        $team = $user->team;

        $existingAssociation = DB::table('championship_team')
        ->where('team_id', $team->id)
        ->exists();


        if ($existingAssociation) {
            return response()->json([
                'code' => 400,
                'message' => 'Team is already associated with another championship.',
            ],400);
        }

        $championshipId = $request->input('championship_id');

        $existingRequest = ChampionshipRequests::where('team_id', $team->id)
            ->where('championship_id', $championshipId)
            ->first();
    
        if ($existingRequest) {
            return response()->json([

                'code' => 400,
                'message' => 'Request already exists for this championship.',
            ],400);
        }
    
        $championship = Championship::findOrFail($championshipId);

        $teamsCount = $championship->teams()->count();

        if($teamsCount >= 16){


            return response()->json([

                'code' => 400,
                'message' => 'Championship is already full. Cannot accept more teams.',
            ]);

        }


       $team = Team::with('image')->findOrFail($team->id);
       

       $teamImage = null;

        if ($team != null) {
            if ($team->image != null) {
                $teamImage = asset($team->image->path);
            }
        }
        
        $ChampionshipRequests = ChampionshipRequests::create([


            'team_id' => $team->id,
            'championship_id' =>$request->input('championship_id'),
            'teamName' =>$team->teamName,
            'coachName' =>$team->coachName,
            'ibanNumber' =>$request->input('ibanNumber'),
            'coachPhoneNumber' =>$team->coachPhoneNumber,
            'teamImage'=>$teamImage
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
            $user->isCoach='false';
            $user->update();
        }


        $formations = $team->formation;
        
        foreach ($formations as $formation) {
            $formation->delete();
        }
    
        $team->delete();

        return response()->json([

            'code'=>200,
            'message' => 'Team deleted successfully'

        ]);


    }




    public function editTeamPoints(Request $request , string $id){

        $team=Team::findOrFail($id);

        $team->update([

            'points' => $request->input('points'),
        
            
        ]);

        return response()->json([

            'code'=>200,
            'message' => 'Team points updated successfully'

        ]);



    }



    public function editTeamRating(Request $request , string $id){

        $team=Team::findOrFail($id);

        $team->update([

            'rate' => $request->input('rate'),
        
            
        ]);

        return response()->json([

            'code'=>200,
            'message' => 'Team rating updated successfully'

        ]);



    }
    
    
    public function kickPlayer(string $id){

        $user=User::findOrFail($id);
       
       if($user->role_id===1){

            return response()->json([

                'code'=>200,
                'message' => 'Team coach cannot be kicked from the team'
    
            ]);


        }
       
       
        $formation=Formation::where('user_id', $user->id);
        $formation->delete();

    
        $user->update([

            'team_id'=>null,
            'selected'=>"not selected",
            
        ]);


        return response()->json([

            'code'=>200,
            'message' => 'User kicked out of team successfully'

        ]);


    }


    public function makeCoach(string $id){

        $newCoach=User::findOrFail($id);
        $oldCoach = User::find(Auth::id());
    
        if (!$oldCoach) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }
    
        $team = $oldCoach->team;
        
        if(!$team){

            return response()->json([
                'code' => 404,
                'message' => 'No team for this player yet',
            ],200);


        }



        $newCoach->update([

            'role_id' => 1,
            'isCoach'=>'true',
            
        ]);


        $oldCoach->update([

            'role_id' => 0,
            'isCoach'=>'false',
        ]);


        $team->update([

            'coachName' => $newCoach->fullName,
            'coachPhoneNumber'=>$newCoach->phoneNumber,
            'coachEmail' => $newCoach->email,
            'user_id'=>$newCoach->id,
            
        ]);


        return response()->json([

            'code'=>200,
            'message' => 'Team coach updated successfully'

        ]);



    }


}
