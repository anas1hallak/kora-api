<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ChampionshipRequests;

class ChampionshipRequestsController extends Controller
{
   
    public function getAllChampionshipRequests(string $id) {
       
        $championshipRequests = ChampionshipRequests::where('championship_id', $id)->get();
    
        return response()->json([
            'status' => 200,
            'championshipRequests' => $championshipRequests,
        ]);
    }




}
