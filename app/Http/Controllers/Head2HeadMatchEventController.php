<?php

namespace App\Http\Controllers;

use App\Models\Head2HeadMatch;
use App\Models\Head2HeadMatchEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Head2HeadMatchEventController extends Controller
{


    public function addH2HEvent(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'Head2HeadMatch_id' => 'required',
            'team_id' => 'required',
            'user_id' => 'required',
            'time' => 'required',
            'type' => 'required'


        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }


        $event = Head2HeadMatchEvent::create([


            'Head2HeadMatch_id' => $request->input('Head2HeadMatch_id'),
            'team_id' => $request->input('team_id'),
            'user_id' => $request->input('user_id'),
            'time' => $request->input('time'),
            'type' => $request->input('type'),



        ]);


        $user = User::findOrFail($request->input('user_id'));
        $head2HeadMatch = Head2HeadMatch::findOrFail($request->input('Head2HeadMatch_id'));

        $eventType = $request->input('type');

        switch ($eventType) {
            case 'Goal':
                $user->goals += 1;

                if ($request->input('team_id') == $head2HeadMatch->team1_id) {

                    $head2HeadMatch->update([


                        'goals1' => $head2HeadMatch->goals1 + 1,


                    ]);
                } else if ($request->input('team_id') == $head2HeadMatch->team2_id) {

                    $head2HeadMatch->update([


                        'goals2' => $head2HeadMatch->goals2 + 1,


                    ]);
                }

                break;


            case 'Yellow Card':
                $user->yellowCards += 1;
                break;
            case 'Red Card':
                $user->redCards += 1;
                break;
        }
        $user->save();

        return response()->json([
            'code' => 200,
            'message' => 'event created successfully.',
        ], 200);
    }
}
