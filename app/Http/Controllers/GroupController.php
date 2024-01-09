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
           
            for ($j = 0; $j < 6; $j++) {

                $gmatch = new Gmatch([

                    'date' => null,
                    'time' => null,
                    'location' => null,
                    'stad' => null,

                    'team1_id' => null,
                    'team2_id' => null,
                ]);

                $group->matches()->save($gmatch);
            }

            

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

                    'teamName' => $team->teamName
                ]);

                $this->insertTeamsIntoMatches($group->id,$team->id);


                return response()->json([
                    'code' => 200,
                    'message' => 'Team inserted into the group successfully.',
                ]);
            }
        }
    
       
        return ;
    }


    public function insertTeamsIntoMatches(string $groupId, string $teamId)
{
    $group = Group::findOrFail($groupId);
    $newTeam = Gteam::findOrFail($teamId);

    // Get all teams in the group
    $teams = $group->teams()->whereNotNull('teamName');
    $count = $group->teams()->whereNotNull('teamName')->count();


    // Get matches with null team IDs
    $matches = $group->matches()->whereNull('team1_id')->whereNull('team2_id')->get();

    // Ensure there are enough matches and teams to create pairings
    if ($count < 2) {
        return;
    }

    // Round-robin algorithm to pair teams in matches
    foreach ($teams as $existingTeam) {
        // Skip pairing the new team with itself
        if ($newTeam->id !== $existingTeam->id) {
            // Find the first match with null team IDs
            $match = $matches->first(function ($match) {
                return $match->team1_id === null && $match->team2_id === null;
            });

            if ($match) {
                // Update the match with team pairings
                $match->update([
                    'team1_id' => $existingTeam->id,
                    'team2_id' => $newTeam->id,
                ]);
            }
        }
    }

    return response()->json([
        'code' => 200,
        'message' => 'Team inserted into matches successfully.',
    ]);
}







public function createGroupMatches(string $groupId)
{
    $group = Group::findOrFail($groupId);

    // Get all teams in the group
    $teams = $group->teams;
    $count = $group->teams()->whereNotNull('teamName')->count();
    // Ensure there are at least 2 teams to create matches
    if ($count < 4) {
        return;
    }

    // Create matches for each pair of teams
    for ($i = 0; $i < count($teams); $i++) {
        for ($j = $i + 1; $j < count($teams); $j++) {
            $match = new Gmatch([
                'team1_id' => $teams[$i]->id,
                'team2_id' => $teams[$j]->id,
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

        foreach ($group->teams as $team) {
            $team = $group->teams();
    
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
    
   



   

}
