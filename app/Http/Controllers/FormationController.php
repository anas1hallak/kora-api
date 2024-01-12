<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Team;
use Illuminate\Http\Request;

class FormationController extends Controller
{
    public function getFormation(string $id){

        $team=Team::findOrFail($id);
        $formation=$team->formation;

        return response()->json([

            'code'=>200,
            'formation'=>$formation,
        
        ]);


    }



    public function editFormation(Request $request){


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
