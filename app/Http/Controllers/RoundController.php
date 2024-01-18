<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Maatch;
use App\Models\Round;
use App\Models\Team;
use Illuminate\Http\Request;

class RoundController extends Controller
{
    

    public function createTree(string $id){

        $championship=Championship::findOrFail($id);

      
        for ($i=1; $i<=3; $i++){

            $round = new Round([
                'round' => $i,
            ]);
    
            $championship->rounds()->save($round);
           

        if ($i === 1) {

            for ($j = 0; $j < 4; $j++) {

            
                $match = new Maatch([

                    'date' => null,
                    'time' => null,
                    'location' => null,
                    'stad' => null,

                    'team1_id' => null,
                    'team2_id' => null,
                ]);

                $round->matches()->save($match);
            }
        }

        if ($i === 2) {

            for ($j = 0; $j < 2; $j++) {

            
                $match = new Maatch([

                    'date' => null,
                    'time' => null,
                    'location' => null,
                    'stad' => null,

                    'team1_id' => null,
                    'team2_id' => null,
                ]);

                $round->matches()->save($match);
            }
        }

        if ($i === 3) {

                $match = new Maatch([

                    'date' => null,
                    'time' => null,
                    'location' => null,
                    'stad' => null,

                    'team1_id' => null,
                    'team2_id' => null,
                ]);

                $round->matches()->save($match);
            
        }
        }

         
        return ;



    }




    public function insertTeamIntoTree(string $id)
    {
        $championship = Championship::findOrFail($id);

        $topTeamsArray = [];

        foreach ($championship->groups as $group) {
            
            $topTeams = $group->teams()->orderByDesc('points')->orderByDesc('goals')->pluck('team_id')->toArray();

            $topTeamsArray = array_merge($topTeamsArray, $topTeams);
        }

        shuffle($topTeamsArray);



        $R1matches = $championship->rounds()->where('round', 1)->first()->matches;

        $j=0;

        foreach ($R1matches as $match) {

            $team1 = $topTeamsArray[$j * 2];
            $team2 = $topTeamsArray[$j * 2 + 1];
        
            $match->update([
                
                'team1_id' => $team1,
                'team2_id' => $team2,
            ]);
        
            $j++;
        }


        $championship->update([

            'status' =>'Elimination Stage'
            
        ]);


        return ;
    }



    public function getTree(string $id){

        $championship=Championship::findOrFail($id);

        foreach ($championship->rounds as $round) {
            foreach ($round->matches as $match) {
                $teams = $match->teams();
        
                foreach ($teams as $team) {
                    if($team!=null){

                        $team=$team->image;

                    }
                }
            }
        }
        return response()->json([

            'code'=>200,
            'message' => 'championship tree returned successfully',
            'championship' =>$championship ,
        ]);



    }



    public function editRoundMatches(Request $request, string $id){


        $match=Maatch::findOrFail($id);

        $match->update([

            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'location' => $request->input('location'),
            'stad' =>$request->input('stad'),
            'winner'=>$request->input('winner'),
            
        ]);

        $team = Team::FindOrFail($request->input('winner'));

        if ($team) {
            $team->update([
                'wins' => $team->wins + 1
            ]);
        }

        return response()->json([
    
            'code'=>200,
            'message' => 'round match updated successfully',
        ]);




    }


    public function getRoundMatchDetails(string $id){

        $match=Maatch::findOrFail($id);
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

    


}
