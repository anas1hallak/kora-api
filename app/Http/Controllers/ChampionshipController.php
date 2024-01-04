<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Championshipimage;
use App\Models\Round;
use App\Models\Maatch;
use App\Models\ChampionshipRequests;

use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Expr\Cast\String_;
use Symfony\Component\Console\Input\Input;

class ChampionshipController extends Controller
{
   
    public function createChampionship(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'championshipName' => 'required',
            'numOfParticipants' => 'required',
            'prize1' => 'required',
            'prize2' => 'required',
            'entryPrice' => 'required',
            'startDate' => 'required',
            'endDate'=>'required',
            
              
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }


        $championship = Championship::create([

            'championshipName' => $request->input('championshipName'),
            'numOfParticipants' => $request->input('numOfParticipants'),
            'prize1' =>$request->input('prize1'),
            'prize2'=>$request->input('prize2'),
            'entryPrice'=>$request->input('entryPrice'),
            'startDate'=>$request->input('startDate'),
            'endDate'=>$request->input('endDate'),


        ]);


        return response()->json([

            'code'=>200,
            'message' => 'Championship created successfully',
        
        ]);

        
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            $fileName = $file->getClientOriginalName();
            $fileName = date('His') . $fileName;
            $path = $request->file('image')->storeAs('images', $fileName, 'public');
            $imageModel = new Championshipimage;
            $imageModel->path = $path; 
            $championship->image()->save($imageModel);
        }
    
        $championship->load('image');

        

        return response()->json([

            'code'=>200,
            'message' => 'Championship created successfully',
        
        ]);
    }


    


    public function getChampionship(string $id)
    {


        $championship=Championship::findOrFail($id);
        $championship->teams;

        return response()->json([

            'code'=>200,
            'championship'=>$championship,
        
        ]);



    }



    // public function getAllChampionships()
    // {

    //     $championships=Championship::all();

    //     return response()->json([

    //         'code'=>200,
    //         'championships'=>$championships,
        
    //     ]);



    // }



    public function getAllChampionships()
{
    $perPage = request()->input('per_page', 10);

    $championships = Championship::paginate($perPage);

    return response()->json([
        'code' => 200,
        'data' => [
            'championships' => $championships->items(),
            'pagination' => [
                'total' => $championships->total(),
                'per_page' => $championships->perPage(),
                'current_page' => $championships->currentPage(),
                'last_page' => $championships->lastPage(),
                'from' => $championships->firstItem(),
                'to' => $championships->lastItem(),
            ],
        ],
    ]);
}






    

    public function addTeamsToChampionship(string $id){


        $championshipRequest=ChampionshipRequests::findOrFail($id);

        $championship=Championship::findOrFail($championshipRequest->championship_id);
        $championship->teams()->attach($championshipRequest->team_id);

        return response()->json([

            'code'=>200,
            'message' => 'Team aded to championship successfully',
        
        ]);

        



    }

    public function getTree(string $id){

        $championship=Championship::findOrFail($id);

        foreach ($championship->rounds as $round) {
            foreach ($round->matches as $match) {
                $teams = $match->teams();
                
            }
        }       

        return response()->json([

            'code'=>200,
            'message' => 'championship tree returned successfully',
            'championship' =>$championship ,
        ]);



    }



    public function createTree(string $id){

        $championship=Championship::findOrFail($id);

        $teams = $championship->teams()->pluck('teams.id')->toArray();

        for ($i=1; $i<=7; $i++){

            $round = new Round([
                'round' => $i,
            ]);
    
            $championship->rounds()->save($round);
           

        if ($i === 1) {

            

            for ($j = 0; $j < 4; $j++) {

                $team1 = $teams[$j * 2];
                $team2 = $teams[$j * 2 + 1];

                $match = new Maatch([

                    'date' => null,
                    'time' => null,
                    'location' => null,
                    'stad' => null,

                    'team1_id' => $team1,
                    'team2_id' => $team2,
                ]);

                $round->matches()->save($match);
            }
        }

        if ($i === 7) {

            

            for ($j = 4; $j < 8; $j++) {
                
                $team1 = $teams[$j * 2];
                $team2 = $teams[$j * 2 + 1];

                $match = new Maatch([

                    'date' => null,
                    'time' => null,
                    'location' => null,
                    'stad' => null,

                    'team1_id' => $team1,
                    'team2_id' => $team2,
                ]);

                $round->matches()->save($match);
            }
        }


        if ($i === 2 || $i === 6 ) {

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
    
        if ($i === 3 || $i === 5) {

            for ($j = 0; $j < 1; $j++) {
               

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

        if ($i === 4) {

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

         
        return response()->json([

            'code'=>200,
            'message' => 'championship tree created successfully',
           
        ]);



    }




    public function updateRound1MatchesInfo(string $id)
    {
        $championship = Championship::findOrFail($id);

        $matches = $championship->rounds()->where('round', 1)->first()->matches;

        $teams = $championship->teams()->pluck('teams.id')->toArray();

        $j=0;

        foreach ($matches as $match) {

            $team1 = $teams[$j * 2];
            $team2 = $teams[$j * 2 + 1];

            $match->update([

                'team1_id' => $team1,
                'team2_id' => $team2,

            ]);

            $j++;
        }

        return response()->json([
            'code' => 200,
            'message' => 'Round 1 matches information updated successfully',
        ]);
    }




}
