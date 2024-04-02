<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormationController extends Controller
{


    public function getFormation(String $id)
    {

        $team = Team::findOrFail($id);
        $formations = $team->formation()->with('user')->get();

        foreach ($formations as $formation) {

            $formation->imagePath = $formation->user->image ? asset('/storage/' . $formation->user->image->path) : null;
            $formation->skills = $formation->user->elo;

            unset($formation->user->image);
            unset($formation['user']);
        }

        return response()->json([

            'code' => 200,
            'formation' => $formations,

        ]);
    }


    public function editFormation(Request $request)
    {


        $formationData = $request->input('formation');


        foreach ($formationData as $formation) {

            $formationId = $formation['id'];
            $position = $formation['position'];

            Formation::where('id', $formationId)->update(['position' => $position]);
        }

        return response()->json([
            'code' => 200,
            'message' => 'Formation updated successfully',
        ]);
    }
}
