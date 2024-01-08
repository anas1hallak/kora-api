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
                return response()->json([
                    'code' => 200,
                    'message' => 'Team inserted into the group successfully.',
                ]);
            }
        }
    
       
        return ;
    }







}
