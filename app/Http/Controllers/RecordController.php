<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\ChampionshipRecord;
use App\Models\Head2HeadMatch;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{



    public function getH2HRecords(String $id)
    {
    
        $team = Team::find($id);
        
        if(!$team){

            return response()->json([
                'code' => 404,
                'message' => 'No team found',
            ],200);


        }
    
        $perPage = 10;

        $head2HeadMatches = Head2HeadMatch::with(['team1', 'team2'])
        ->where('status', 'ended')
        ->where(function ($query) use ($team) {
            $query->where('team1_id', $team->id)
                ->orWhere('team2_id', $team->id);
        })
        ->paginate($perPage);


        
       

        $formattedMatches = [];

        foreach ($head2HeadMatches as $match) {

            if (!$match->team1 || !$match->team2) {
                continue;
            }

            $formattedMatches[] = [

                
                'id' => $match->id,
                'date' => $match->date,
                'time' => $match->time,
                'location' => $match->location,
                'stad' => $match->stad,
                'winner' => $match->winner,
                'goals1' => $match->goals1,
                'goals2' => $match->goals2,
                'status' => $match->status,
            
                'team1' => [
                    'id' => $match->team1->id,
                    'teamName' => $match->team1->teamName,
                    'imagePath' => $match->team1->image ? asset('/storage/' . $match->team1->image->path) : null,
                ],
                'team2' => [
                    'id' => $match->team2->id,
                    'teamName' => $match->team2->teamName,
                    'imagePath' => $match->team2->image ? asset('/storage/' . $match->team2->image->path) : null,
                ],
            ];
        }

        // Return the paginated matches
        return response()->json([
            'code' => 200,
            'head2HeadMatches' => $formattedMatches,
            'pagination' => [
                'total' => $head2HeadMatches->total(),
                'per_page' => $head2HeadMatches->perPage(),
                'current_page' => $head2HeadMatches->currentPage(),
                'last_page' => $head2HeadMatches->lastPage(),
                'from' => $head2HeadMatches->firstItem(),
                'to' => $head2HeadMatches->lastItem(),
            ],
        ]);
    }



    public function getChampionshipRecords(String $id){

        
        $team = Team::find($id);
        
        if(!$team){

            return response()->json([
                'code' => 404,
                'message' => 'No team found',
            ],200);


        }

        $perPage = 10;

        $championshipRecords = ChampionshipRecord::where('team_id', $team->id)->paginate($perPage);

      

        return response()->json([
            'code' => 200,
            'championshipRecords' => $championshipRecords,
            'pagination' => [
                'total' => $championshipRecords->total(),
                'per_page' => $championshipRecords->perPage(),
                'current_page' => $championshipRecords->currentPage(),
                'last_page' => $championshipRecords->lastPage(),
                'from' => $championshipRecords->firstItem(),
                'to' => $championshipRecords->lastItem(),
            ],


        ]);


        


    }


    public function createChampionshipRecord(string $id,string $team_id){

        $championship = Championship::findOrFail($id);

        ChampionshipRecord::create([
            'team_id' => $team_id,
            'championship_id' => $championship->id,
            'championshipName' => $championship->championshipName,
            'numOfParticipants' => $championship->numOfParticipants,
            'prize1' => $championship->prize1,
            'prize2' => $championship->prize2,
            'entryPrice' => $championship->entryPrice,
            'startDate' => $championship->startDate,
            'endDate' => $championship->endDate,
            'status'=>$championship->status,
            'imagePath' =>$championship->image ? asset('/storage/'. $championship->image->path) : null,
            'teamsCount' => 16,
            'termsAndConditions' => $championship->termsAndConditions,
            'firstWinner' => $championship->firstWinner,
            'secondWinner' => $championship->secondWinner,
        ]);
    
        return ;

    }



}
