<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function searchTeam(Request $request)
    {
        $query = $request->input('query');

        // Perform the search using the query parameter
        $teams = Team::where('teamName', 'LIKE', "%$query%")->get();

        return response()->json([
            'code' => 200,
            'teams' => $teams,
            'message' => 'Teams retrieved successfully',
        ]);
    }


    public function searchUser(Request $request)
    {
        $query = $request->input('query');

        // Perform the search using the query parameter
        $users = User::where('fullName', 'LIKE', "%$query%")->get();

        return response()->json([
            'code' => 200,
            'users' => $users,
            'message' => 'Users retrieved successfully',
        ]);
    }


    public function searchChampionship(Request $request)
    {
        $query = $request->input('query');

        // Perform the search using the query parameter
        $championships = Championship::where('championshipName', 'LIKE', "%$query%")->get();

        return response()->json([
            'code' => 200,
            'championships' => $championships,
            'message' => 'Championships retrieved successfully',
        ]);
    }



}
