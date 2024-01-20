<?php

namespace App\Http\Controllers;

use App\Models\Championship;
use App\Models\Championshipimage;
use App\Models\ChampionshipRequests;

use App\Http\Controllers\RoundController;
use App\Http\Controllers\GroupController;
use App\Models\FcmToken;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            return response()->json(['message'=>$validator->errors()->first()],400);
        }

        $championship = Championship::create([

            'championshipName' => $request->input('championshipName'),
            'numOfParticipants' => $request->input('numOfParticipants'),
            'prize1' =>$request->input('prize1'),
            'prize2'=>$request->input('prize2'),
            'entryPrice'=>$request->input('entryPrice'),
            'startDate'=>$request->input('startDate'),
            'endDate'=>$request->input('endDate'),
            //'termsAndConditions'=>$request->input('termsAndConditions'),



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

        $tokens = FcmToken::whereIn('user_id', User::where('role_id', 1)->pluck('id'))->pluck('fcmToken')->toArray();
        $title='New Championship!, Join Now.';
        $body = 'A new championship ' . $request->input('championshipName') . ' has been created! Don\'t miss the chance to join and compete for exciting prizes. Gather your team, register, and showcase your skills on the field.';
        (new PushNotificationController)->sendNotification($tokens,$body,$title);


        return response()->json([

            'code'=>200,
            'message' => 'Championship created successfully',
        
        ]);
    }


    public function championshipProfile()
    {
        // Get the authenticated user
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        
        $team=$user->team;
        
        $championshipId = DB::table('championship_team')
        ->where('team_id', $team->id)
        ->value('championship_id');



        if (!$championshipId) {
            return response()->json(['message' => 'User is not associated with any championship'], 200);
        }


        $championship=Championship::findOrFail($championshipId);

        
        $teamsCount = $championship->teams()->count();
        $imagePath = $championship->image ? asset('/storage/'. $championship->image->path) : null;

        $championship->teamsCount = $teamsCount;
        $championship->imagePath = $imagePath;

        unset($championship['image']);
        

        

    

        return response()->json([
            'code' => 200,
            'message' => 'Championship retrieved successfully',
            'championship' => $championship,
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





    public function getAllChampionships(Request $request)
    {

        
        $perPage = request()->input('per_page', 10);

        $query = $request->query('search');
    
        $championshipsQuery = Championship::with('image');
    
        if ($query) {
            $championshipsQuery->where('championshipName', 'LIKE', "%$query%");
        }
    
        $championships = $championshipsQuery->paginate($perPage);
    

        $championshipData = [];

        foreach ($championships as $championship) {

        
        $imagePath = $championship->image ? asset('/storage/'. $championship->image->path) : null;
            

        $championshipData[] = [

            'id' => $championship->id,
            'championshipName' => $championship->championshipName,
            'numOfParticipants' => $championship->numOfParticipants,
            'prize1' => $championship->prize1,
            'prize2' => $championship->prize2,
            'entryPrice' => $championship->entryPrice,
            'startDate'=>$championship->startDate,
            'endDate'=>$championship->endDate,
            'status'=>$championship->status,
            'imagePath' => $imagePath,
            'teamsCount' => $championship->teams()->count(),

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
        $teamsCount = $championship->teams->count();

        if($teamsCount >= 16){


            return response()->json([
                'code' => 400,
                'message' => 'Championship is already full. Cannot accept more teams.',
            ]);

        }


        $championship->teams()->attach($championshipRequest->team_id);

        (new GroupController)->insertTeamIntoGroup($championship->id,$championshipRequest->team_id);

        $championshipRequest->delete();


        

        $teamsCount = $championship->teams()->count();

        if($teamsCount >= 16){


            $championship->update([

                'status' =>'Group Stage'
                
            ]);

        }

        $team = Team::findOrFail($championshipRequest->team_id);
        $tokens = User::findOrFail($team->user_id)->fcmTokens()->pluck('fcmToken')->toArray();
        $title='Your Team Has Been Accepted !';
        $body = 'Congratulations! Your team has been accepted to participate in the '. $championship->championshipName . ' Championship. Get ready for the action and make your mark on the field. Good luck, coach!';
        (new PushNotificationController)->sendNotification($tokens,$body,$title);

        return response()->json([

            'code'=>200,
            'message' => 'Team aded to championship successfully',
        
        ]);



    }


    public function rejectChampionshipRequest(string $id){


        $championshipRequest=ChampionshipRequests::findOrFail($id);

        $championshipRequest->delete();

        $championship=Championship::findOrFail($championshipRequest->championship_id);


        $team = Team::findOrFail($championshipRequest->team_id);
        $tokens = User::findOrFail($team->user_id)->fcmTokens()->pluck('fcmToken')->toArray();
        $title='Championship Registration Rejected';
        $body = 'We regret to inform you that your team registration for '.$championship->championshipName.' championship has been rejected. We appreciate your interest and hope to see you in future events. If you have any questions, please contact the organizers.';
        (new PushNotificationController)->sendNotification($tokens,$body,$title);


        return response()->json([

            'code'=>200,
            'message' => 'Request deleted successfully',
        
        ]);


    }

   

}
