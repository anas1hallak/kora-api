<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Maatch;
use App\Models\Round;

use Illuminate\Http\Request;

class RoundController extends Controller
{
    

    public function createTree(string $id){

        $championship=Championship::findOrFail($id);

       // $teams = $championship->teams()->pluck('teams.id')->toArray();

        for ($i=1; $i<=5; $i++){

            $round = new Round([
                'round' => $i,
            ]);
    
            $championship->rounds()->save($round);
           

        if ($i === 1||$i===5) {

            

            for ($j = 0; $j < 2; $j++) {

               // $team1 = $teams[$j * 2];
                //$team2 = $teams[$j * 2 + 1];

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

        if ($i === 2||$i === 3||$i === 4) {

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

        $R1matches = $championship->rounds()->where('round', 1)->first()->matches;
        

        $teams = $championship->teams()->pluck('teams.id')->toArray();

        $j=0;

        foreach ($R1matches as $match) {

                if (isset($teams[$j * 2])) {

                    $team1 = $teams[$j * 2];

                    $match->update([

                        'team1_id' => $team1,
                    ]);
                }
                if (isset($teams[$j * 2 + 1])) {

                    $team2 = $teams[$j * 2 + 1];

                    $match->update([

                        'team2_id' => $team2,
                    ]);
                }

            $j++;
        }


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

    


}
