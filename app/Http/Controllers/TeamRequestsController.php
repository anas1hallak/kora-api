<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\TeamRequests;

class TeamRequestsController extends Controller
{



    public function getAllTeamRequests(string $id) {
       
        $TeamRequests = TeamRequests::where('team_id', $id)->get();
    
        return response()->json([
            'status' => 200,
            'TeamRequests' => $TeamRequests,
        ]);
    }


    
}
