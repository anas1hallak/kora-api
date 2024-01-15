<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use Illuminate\Http\Request;

use App\Models\ChampionshipRequests;

class ChampionshipRequestsController extends Controller
{
   
    public function getAllChampionshipRequests(string $id) {
       
        $championshipRequests = ChampionshipRequests::where('championship_id', $id)->get();
        $championship = Championship::findOrFail($id);

        $teamsCount = $championship->teams()->count();

        

        return response()->json([
            'status' => 200,
            'championshipRequests' => $championshipRequests,
            'teamsCount'=>$teamsCount
        ]);
    }




}
