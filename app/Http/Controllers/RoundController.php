<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Maatch;
use App\Models\Round;
use App\Models\Team;
use Illuminate\Http\Request;

class RoundController extends Controller
{
    

    public function createTree(string $id)
    {
        $championship = Championship::findOrFail($id);
    
        for ($i = 1; $i <= 3; $i++) {
    
            $round = new Round([
                'round' => $i,
            ]);
    
            $championship->rounds()->save($round);
    
            // Create matches for the current round
            for ($j = 0; $j < 2 ** (3 - $i); $j++) {
    
                $match = new Maatch([
                    'date' => null,
                    'time' => null,
                    'location' => null,
                    'stad' => null,
                    'team1_id' => null,
                    'team2_id' => null,
                    'position' => $j + 1, // Set the position based on the loop index
                ]);
    
                $round->matches()->save($match);
    
                // Set previous_match_id for the next round matches
                if ($i < 3) {
                    $nextRound = $championship->rounds()->where('round', $i + 1)->first();
    
                    // Check if there is a next round
                    if ($nextRound) {
                        // Find the corresponding match in the next round
                        $nextRoundMatch = $nextRound->matches()->where('position', ceil(($j + 1) / 2))->first();
    
                    }
                }
            }
        }
    
        return;
    }

    





    public function insertTeamIntoTree(string $id)
    {
        $championship = Championship::findOrFail($id);

        $topTeamsArray = [];
        $bottomTeamsArray = [];

        foreach ($championship->groups as $group) {
            
            $topTeams = $group->teams()->orderByDesc('points')->orderByDesc('goals')->take(2)->pluck('team_id')->toArray();

            $topTeamsArray = array_merge($topTeamsArray, $topTeams);



            $bottomTeams = $group->teams()->orderBy('points')->orderBy('goals')->take(2)->pluck('team_id')->toArray();

            $bottomTeamsArray = array_merge($bottomTeamsArray, $bottomTeams);
        }

        shuffle($topTeamsArray);

        $championship->teams()->detach($bottomTeamsArray);


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

                        $imagePath = $team->image ? asset('/storage/'. $team->image->path) : null;
                        $team->imagePath = $imagePath;
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

        $match = Maatch::findOrFail($id);
    
        $match->update([
            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'location' => $request->input('location'),
            'stad' => $request->input('stad'),
            'winner' => $request->input('winner'),
        ]);
    
        $winningTeam = Team::findOrFail($request->input('winner'));

        $losingTeamId = ($match->team1_id === $winningTeam->id) ? $match->team2_id : $match->team1_id;



        $championship=$match->round->championship;
        
        $championship->teams()->detach($losingTeamId);





        $nextRoundNumber = $match->round->round + 1;
    
        if ($nextRoundNumber <= 3) {
            // Retrieve or create the next round
            $nextRound = Round::where('championship_id', $match->round->championship_id)
                ->where('round', $nextRoundNumber)
                ->first();
            
            // Identify the match in the next round based on position
            $nextRoundMatch = $nextRound->matches()->where('position', ceil($match->position / 2))->first();
            
            if ($nextRoundMatch) {
                // Update the next round match with the winning team
                if ($nextRoundMatch->team1_id === null) {
                    $nextRoundMatch->update(['team1_id' => $winningTeam->id]);
                } else {
                    $nextRoundMatch->update(['team2_id' => $winningTeam->id]);
                }
            }
        }
        else {
            // It's the final round, detach the winner from the championship

            $losingTeam = Team::findOrFail($losingTeamId);

           

            $championship->update([

                'firstWinner' =>$winningTeam->teamName,
                'secondWinner' =>$losingTeam->teamName,
                'status' =>'Ended'


                
            ]);
            

            $championship->teams()->detach($winningTeam->id);


        }
        
        return response()->json([
            'code' => 200,
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
