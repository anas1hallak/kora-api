<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Championshipimage;
use App\Models\ChampionshipRequests;

use App\Http\Controllers\RoundController;
use App\Http\Controllers\GroupController;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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


        (new GroupController)->createGroup($championship->id);
        (new RoundController)->createTree($championship->id);

        

        return response()->json([

            'code'=>200,
            'message' => 'Championship created successfully',
        
        ]);
    }


    


    public function getChampionship(string $id)
    {


        $championship=Championship::findOrFail($id);
        $championship->teams;

        $championship->load('image');

        $imagePath = $championship->image ? asset('/storage/'. $championship->image->path) : null;

        $championship->imagePath = $imagePath;

        unset($championship['image']);


        return response()->json([

            'code'=>200,
            'championship'=>$championship,
        
        ]);



    }





    public function getAllChampionships()
    {

        
        $perPage = request()->input('per_page', 10);

        $championships = Championship::with('image')->paginate($perPage);

        $championshipData = [];

        foreach ($championships as $championship) {

        
        $imagePath = $championship->image ? asset('/storage/'. $championship->image->path) : null;
            
        $championshipData[] = [

            'id' => $championship->id,
            'championshipName' => $championship->teamName,
            'numOfParticipants' => $championship->points,
            'prize1' => $championship->prize1,
            'prize2' => $championship->prize2,
            'entryPrice' => $championship->entryPrice,
            'startDate'=>$championship->startDate,
            'endDate'=>$championship->endDate,
            'imagePath' => $imagePath,
        ];
    }

        return response()->json([
            'code' => 200,
            'data' => [
                'championships' => $championshipData,
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

        (new GroupController)->insertTeamIntoGroup($championship->id,$championshipRequest->team_id);

        $championshipRequest->delete();

        return response()->json([

            'code'=>200,
            'message' => 'Team aded to championship successfully',
        
        ]);



    }


    public function rejectChampionshipRequest(string $id){


        $championshipRequest=ChampionshipRequests::findOrFail($id);

        $championshipRequest->delete();

        return response()->json([

            'code'=>200,
            'message' => 'Request deleted successfully',
        
        ]);


    }

   

}
