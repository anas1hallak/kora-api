<?php

namespace App\Http\Controllers;

use App\Models\Championship;

use App\Models\Championshipimage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;
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
            'starteDate' => 'required',
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
            'starteDate'=>$request->input('starteDate'),
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
            $championship->championshipImage()->save($imageModel);
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

        return response()->json([

            'code'=>200,
            'championship'=>$championship,
        
        ]);



    }



    public function getAllChampionships()
    {

        $championships=Championship::all();

        return response()->json([

            'code'=>200,
            'championships'=>$championships,
        
        ]);



    }





    

    public function addTeamsToChampionship(Request $request){


        $championship=Championship::findOrFail($request->Input('championship_id'));
        $championship->teams()->attach($request->input('team_id'));

        return response()->json([

            'code'=>200,
            'message' => 'Team aded to championship successfully',
        
        ]);



    }




}
