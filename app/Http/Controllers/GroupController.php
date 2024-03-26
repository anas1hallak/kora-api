<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Gmatch;
use App\Models\Group;
use App\Models\Gteam;
use App\Models\Team;
use Illuminate\Http\Request;

class GroupController extends Controller
{


    public function createGroup(string $id){

        $championship=Championship::findOrFail($id);
        $groupNames = ['Group A', 'Group B', 'Group C', 'Group D'];

        for ($i=0; $i<4; $i++){

            $group = new Group([
                'group' => $groupNames[$i],
            ]);
    
            $championship->groups()->save($group);
            

            for ($j = 0; $j < 4; $j++) {

                $gteam = new Gteam([

                    'teamName' => null,
                    
                ]);

                $group->teams()->save($gteam);
            }
        
       
        }

         
        return ;



    }


    public function insertTeamIntoGroup(string $id, string $team_id)
    {
        $championship = Championship::findOrFail($id);

        $team =Team::findOrFail($team_id);

        $groups = $championship->groups;

        foreach ($groups as $group) {
            
            $teamWithNullName = $group->teams()->whereNull('teamName')->first();
    
            if ($teamWithNullName) {
               
                $teamWithNullName->update([


                    'team_id'=>$team->id,
                    'teamName' => $team->teamName
                ]);

                $this->createGroupMatches($group->id);


                return ;
            }
        }
    
       
        return ;
    }






    public function createGroupMatches(string $groupId)
    {
        $group = Group::findOrFail($groupId);

        // Get all teams in the group


        $teams = $group->teams->pluck('team_id')->toArray();
        $count = $group->teams()->whereNotNull('teamName')->count();
        // Ensure there are at least 2 teams to create matches
        if ($count < 4) {
            return;
        }

        // Create matches for each pair of teams
        for ($i = 0; $i < count($teams); $i++) {
            for ($j = $i + 1; $j < count($teams); $j++) {
                $match = new Gmatch([
                    'team1_id' => $teams[$i],
                    'team2_id' => $teams[$j],
                    'date' => null,
                    'time' => null,
                    'location' => null,
                    'stad' => null,
                ]);

                $group->matches()->save($match);
            }
        }

        return response()->json([
            'code' => 200,
            'message' => 'Matches created successfully.',
        ]);
    }




    public function getGroups(string $id){

        $championship=Championship::findOrFail($id);

        foreach ($championship->groups as $group) {
            foreach ($group->teams as $gteam) {
               
                $team = $gteam->team;
    
                if ($team) {

                    $image = $team->image;
    
                    $imagePath = $image ? asset('/storage/' . $image->path) : null;
                    $gteam->imagePath = $imagePath;

                }
                unset($gteam['team']);

            }
        }



        
            return response()->json([

                'code'=>200,
                'message' => 'championship groups returned successfully',
                'championship' =>$championship ,
            ]);
        }


    public function getGroupMatches(string $id){

        $group=Group::findOrFail($id);
    
        foreach ($group->matches as $match) {
            $teams = $match->teams();
    
            foreach ($teams as $team) {
                if($team!=null){
    
                    $team=$team->image;
    
                }
            }
        }
            return response()->json([
    
                'code'=>200,
                'message' => 'group matches returned successfully',
                'group' =>$group ,
            ]);
        }


        public function getGroupMatchDetails(string $id){

            $match=Gmatch::findOrFail($id);
            $teams = $match->teams();
            
            foreach ($teams as $team) {
                if($team!=null){
    
                    $team=$team->image;
    
                }
            }
    
            return response()->json([
            
                'code'=>200,
                'match' => $match,
            ]);   
    
        }
    
    public function editGroupMatches(Request $request, string $id){


        $match=Gmatch::findOrFail($id);
       
        $match->update([

            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'location' => $request->input('location'),
            'stad' =>$request->input('stad'),
            'winner'=>$request->input('winner'),
            
        ]);

        if($request->input('winner')==null){

            return response()->json([
                'code' => 200,
                'message' => 'round match updated successfully',
            ]);

        }

        $group = $match->group;

        if ($group) {
            // Retrieve the team in the group with the specified team_id
            $gteam = $group->teams()->where('team_id', $request->input('winner'))->first();

            if ($gteam) {
                // Update the points for the team in the group
                $gteam->update([
                    'points' => $gteam->points + 3
                ]);
            }
        }

        $team = Team::findOrFail($request->input('winner'));

        if ($team) {
            $team->update([
                'wins' => $team->wins + 1
            ]);
        }


        //to check if the group stage has ended and move to the elimination stage
        $championshipId = $match->group->championship->id;

        $allMatchesFinished = $this->areGroupMatchesFinished($championshipId);

        if ($allMatchesFinished) {

            (new RoundController)->insertTeamIntoTree($championshipId);
        }



        return response()->json([
    
            'code'=>200,
            'message' => 'group match updated successfully',
        ]);



    }



    public function areGroupMatchesFinished($championshipId)
    {
        $championship = Championship::findOrFail($championshipId);

        //checks if there is a match with no winner
        foreach ($championship->groups as $group) {
            foreach ($group->matches as $match) {
                if (!$match->winner) {
                    return false;
                }
            }
        }

        return true;
    }
   

}
