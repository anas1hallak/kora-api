<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\ChampionshipRecord;
use App\Models\Head2HeadMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{



    public function getH2HRecords()
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
    
        $perPage = 10;

        $head2HeadMatches = Head2HeadMatch::with(['team1', 'team2'])
        ->where('status', 'ended')
        ->whereIn('team1_id', [$team->id])
        ->orWhereIn('team2_id', [$team->id])
        ->paginate($perPage);

        $formattedMatches = [];

        foreach ($head2HeadMatches as $match) {
            $formattedMatches[] = [

                
                'id' => $match->id,
                'date' => $match->date,
                'time' => $match->time,
                'location' => $match->location,
                'stad' => $match->stad,
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



    public function getChampionshipRecords(){

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

        $championshipRecords = ChampionshipRecord::where('team_id', $team->id)->get();

        if ($championshipRecords->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'No championship records found for the specified team ID.',
            ], 404);
        }

        return response()->json([
            'code' => 200,
            'championshipRecords' => $championshipRecords,
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
            'termsAndConditions' => $championship->termsAndConditions,
            'firstWinner' => $championship->firstWinner,
            'secondWinner' => $championship->secondWinner,
        ]);
    
        return ;

    }



}
