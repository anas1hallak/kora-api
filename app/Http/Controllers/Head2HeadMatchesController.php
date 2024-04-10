<?php

namespace App\Http\Controllers;

use App\Models\Head2HeadMatch;
use App\Models\Head2HeadMatchEvent;
use App\Models\Head2HeadMatchImage;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class Head2HeadMatchesController extends Controller
{

    public function createH2HMatch(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'team1_id' => 'required',
            'team2_id' => 'required',
            'date' => 'required',
            'time' => 'required'

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }


        $teamId = $request->input('team1_id');
        $existingMatch = Head2HeadMatch::where(function ($query) use ($teamId) {
            $query->where('team1_id', $teamId)
                ->orWhere('team2_id', $teamId);
        })->whereNotIn('status', ['ended'])->exists();

        if ($existingMatch) {
            return response()->json([
                'code' => 400,
                'message' => 'Team already has an ongoing or pending head-to-head match.'
            ], 200);
        }


        $Head2HeadMatch = Head2HeadMatch::create([


            'team1_id' => $request->input('team1_id'),
            'team2_id' => $request->input('team2_id'),
            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'status' => "pending_acceptance",



        ]);

        $team = Team::findOrFail($request->input('team2_id'));
        $tokens = User::findOrFail($team->user_id)->fcmTokens()->pluck('fcmToken')->toArray();
        $title = 'Head-to-Head Match Invitation';
        $body = 'You received a head-to-head match invitation from ' . $team->teamName;
        (new PushNotificationController)->sendNotification($tokens, $body, $title);


        return response()->json([

            'code' => 200,
            'message' => 'invite sent successfully',

        ]);
    }







    public function createH2HMatchDashboard(Request $request)
    {


        $validator = Validator::make($request->all(), [

            'team1_id' => 'required',
            'team2_id' => 'required',
            'date' => 'required',
            'time' => 'required',
            'location' => 'required',
            'stad' => 'required',

        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 400);
        }


        $teamId = $request->input('team1_id');
        $existingMatch = Head2HeadMatch::where(function ($query) use ($teamId) {
            $query->where('team1_id', $teamId)
                ->orWhere('team2_id', $teamId);
        })->whereNotIn('status', ['ended'])->exists();

        if ($existingMatch) {
            return response()->json([
                'code' => 400,
                'message' => 'Team already has an ongoing or pending head-to-head match.'
            ], 200);
        }


        $Head2HeadMatch = Head2HeadMatch::create([


            'team1_id' => $request->input('team1_id'),
            'team2_id' => $request->input('team2_id'),
            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'location' => $request->input('location'),
            'stad' => $request->input('stad'),
            'status' => "approved",



        ]);



        return response()->json([

            'code' => 200,
            'message' => 'head-to-head match created successfully',

        ]);
    }







    public function getTeamH2HMatch()
    {
        $user = User::find(Auth::id());

        if (!$user) {
            return response()->json([
                'code' => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        $team = Team::findOrFail($user->team_id);

        if (!$team) {

            return response()->json([
                'code' => 404,
                'message' => 'No team for this player yet',
            ], 200);
        }

        $teamId = $team->id;

        $head2HeadMatches = Head2HeadMatch::with(['team1', 'team2'])
            ->where(function ($query) use ($teamId) {
                $query->where('team1_id', $teamId)
                    ->orWhere('team2_id', $teamId);
            })
            ->whereNotIn('status', ['ended'])
            ->get();



        if ($head2HeadMatches->isEmpty()) {
            return response()->json([
                'code' => 404,
                'message' => 'No head-to-head match found for the team.',
            ], 404);
        }

        $formattedMatches = [];

        foreach ($head2HeadMatches as $match) {

            if (!$match->team1 || !$match->team2) {
                continue;
            }

            $formattedMatches[] = [
                'id' => $match->id,
                'date' => $match->date,
                'time' => $match->time,
                'location' => $match->location,
                'stad' => $match->stad,
                'winner' => $match->winner,
                'goals1' => $match->goals1,
                'goals2' => $match->goals2,
                'status' => $match->status,
                'ibanNumber1' => $match->ibanNumber1,
                'ibanNumber2' => $match->ibanNumber2,
                'team1' => [
                    'id' => $match->team1->id,
                    'teamName' => $match->team1->teamName,
                    'imagePath' => $match->team1->image ? asset('/storage/' . $match->team1->image->path) : null,
                ],
                'team2' => [
                    'id' => $match->team2->id,
                    'teamName' => $match->team2->teamName,
                    'imagePath' => $match->team2->image ? asset('/storage/' . $match->team2->image->path) : null,
                ],
            ];
        }

        return response()->json([
            'code' => 200,
            'head2HeadMatches' => $formattedMatches,
        ]);
    }




    public function getAllH2HMatches()
    {


        $perPage = 10;

        $head2HeadMatches = Head2HeadMatch::with(['team1', 'team2'])
            ->where('status', 'approved')
            ->paginate($perPage);

        $formattedMatches = [];

        foreach ($head2HeadMatches as $match) {

            if (!$match->team1 || !$match->team2) {
                continue;
            }

            $formattedMatches[] = [


                'id' => $match->id,
                'date' => $match->date,
                'time' => $match->time,
                'location' => $match->location,
                'stad' => $match->stad,
                'winner' => $match->winner,
                'goals1' => $match->goals1,
                'goals2' => $match->goals2,
                'status' => $match->status,

                'team1' => [
                    'id' => $match->team1->id,
                    'teamName' => $match->team1->teamName,
                    'imagePath' => $match->team1->image ? asset('/storage/' . $match->team1->image->path) : null,
                ],
                'team2' => [
                    'id' => $match->team2->id,
                    'teamName' => $match->team2->teamName,
                    'imagePath' => $match->team2->image ? asset('/storage/' . $match->team2->image->path) : null,
                ],
            ];
        }

        // Return the paginated matches
        return response()->json([
            'code' => 200,
            'head2HeadMatches' => $formattedMatches,
            'pagination' => [
                'total' => $head2HeadMatches->total(),
                'per_page' => $head2HeadMatches->perPage(),
                'current_page' => $head2HeadMatches->currentPage(),
                'last_page' => $head2HeadMatches->lastPage(),
                'from' => $head2HeadMatches->firstItem(),
                'to' => $head2HeadMatches->lastItem(),
            ],
        ]);
    }



    public function acceptH2HMatch(String $id)
    {

        $Head2HeadMatch = Head2HeadMatch::findOrFail($id);

        $Head2HeadMatch->update([


            'status' => "pending_payment",


        ]);


        $team = Team::findOrFail($Head2HeadMatch->team1_id);
        $tokens = User::findOrFail($team->user_id)->fcmTokens()->pluck('fcmToken')->toArray();
        $title = 'Head-to-Head Match Invitation Accepted';
        $body = 'Your head-to-head match invitation has been accepted by the opposing team, Please submit your payment method.';
        (new PushNotificationController)->sendNotification($tokens, $body, $title);



        return response()->json([

            'code' => 200,
            'message' => 'invite approved successfully',

        ]);
    }


    public function rejectH2HMatch(String $id)
    {

        $Head2HeadMatch = Head2HeadMatch::findOrFail($id);

        $team = Team::findOrFail($Head2HeadMatch->team1_id);
        $tokens = User::findOrFail($team->user_id)->fcmTokens()->pluck('fcmToken')->toArray();
        $title = 'Head-to-Head Match Invitation Rejected';
        $body = 'Your head-to-head match invitation has been rejected by the opposing team.';
        (new PushNotificationController)->sendNotification($tokens, $body, $title);

        $Head2HeadMatch->delete();

        return response()->json([

            'code' => 200,
            'message' => 'invite rejected successfully',

        ]);
    }



    public function selectPaymentMethod(Request $request, String $id)
    {


        $Head2HeadMatch = Head2HeadMatch::findOrFail($id);


        if ($Head2HeadMatch->team1_id == $request->input('team_id')) {

            $Head2HeadMatch->update([


                'ibanNumber1' => $request->input('ibanNumber'),


            ]);
        } elseif ($Head2HeadMatch->team2_id == $request->input('team_id')) {


            $Head2HeadMatch->update([


                'ibanNumber2' => $request->input('ibanNumber'),


            ]);
        }


        (new Head2HeadRequestsController)->submitH2Hrequest($id);



        return response()->json([

            'code' => 200,
            'message' => 'payment submited successfully',

        ]);
    }




    public function getH2HMatchEvents(string $id)
    {
        $head2HeadMatch = Head2HeadMatch::find($id);

        if (!$head2HeadMatch) {
            return response()->json([
                'code' => 404,
                'message' => 'Head-to-head match not found.',
            ], 404);
        }



        $events = Head2HeadMatchEvent::with('team', 'user')
            ->where('Head2HeadMatch_id', $id)
            ->get();



        $formattedEvents = [];

        foreach ($events as $event) {
            $formattedEvents[] = [


                'id' => $event->id,
                'playerName' => $event->user->fullName,
                'teamName' => $event->team->teamName,
                'time' => $event->time,
                'type' => $event->type,


            ];
        }



        return response()->json([
            'code' => 200,
            'events' => $formattedEvents,
        ]);
    }







    public function getH2HMatchDetails(string $id)
    {
        $head2HeadMatch = Head2HeadMatch::with(['team1', 'team2','images'])
            ->find($id);

        if (!$head2HeadMatch) {
            return response()->json([
                'code' => 404,
                'message' => 'Head-to-head match not found.',
            ], 404);
        }

        $events = Head2HeadMatchEvent::with('team', 'user')
            ->where('Head2HeadMatch_id', $id)
            ->get();

        $formattedEvents = [];

        foreach ($events as $event) {
            $formattedEvents[] = [
                'id' => $event->id,
                'playerName' => $event->user->fullName,
                'teamName' => $event->team->teamName,
                'time' => $event->time,
                'type' => $event->type,
            ];
        }

        $formattedMatch = [
            'id' => $head2HeadMatch->id,
            'date' => $head2HeadMatch->date,
            'time' => $head2HeadMatch->time,
            'location' => $head2HeadMatch->location,
            'stad' => $head2HeadMatch->stad,
            'winner' => $head2HeadMatch->winner,
            'goals1' => $head2HeadMatch->goals1,
            'goals2' => $head2HeadMatch->goals2,
            'status' => $head2HeadMatch->status,
            'team1' => [
                'id' => $head2HeadMatch->team1->id,
                'teamName' => $head2HeadMatch->team1->teamName,
                'imagePath' => $head2HeadMatch->team1->image ? asset('/storage/' . $head2HeadMatch->team1->image->path) : null,
            ],
            'team2' => [
                'id' => $head2HeadMatch->team2->id,
                'teamName' => $head2HeadMatch->team2->teamName,
                'imagePath' => $head2HeadMatch->team2->image ? asset('/storage/' . $head2HeadMatch->team2->image->path) : null,
            ],

            'images' => $head2HeadMatch->images->isEmpty() ? [] : $head2HeadMatch->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'imagePath' => asset('/storage/' . $image->path), 
                ];
            }),
        ];

        return response()->json([
            'code' => 200,
            'head2HeadMatch' => $formattedMatch,
            'events' => $formattedEvents,
        ]);
    }




    public function editH2HMatch(Request $request,String $id)
    {

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [

            'date' => 'nullable',
            'time' => 'nullable',
            'location' => 'nullable|string',
            'stad' => 'nullable|string',
            'winner' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }
        // Find the head-to-head match by its ID
        $head2HeadMatch = Head2HeadMatch::findOrFail($id);

        $head2HeadMatch->update([


            'date' => $request->input('date'),
            'time' => $request->input('time'),
            'location' => $request->input('location'),
            'stad' => $request->input('stad'),
            'winner' => $request->input('winner'),


        ]);

        $imagePaths = [];

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                
                $fileName = date('His') . $file->getClientOriginalName();
                $path = $file->storeAs('images', $fileName, 'public');
                $imagePaths[] = $path;
            }

            // Save the image paths to the Image model
            foreach ($imagePaths as $path) {
                $imageModel = new Head2HeadMatchImage();
                $imageModel->path = $path;
                $head2HeadMatch->images()->save($imageModel);
                $head2HeadMatch->load('images');
            }
        }

        // If the winner is chosen, set the status to 'ended'
        if ($request->input('winner')!=null) {

            $head2HeadMatch->update([

                'status' => "ended",
    
            ]);

            $winningTeam = Team::findOrFail($request->input('winner'));
            $losingTeamId = ($head2HeadMatch->team1_id === $winningTeam->id) ? $head2HeadMatch->team2_id : $head2HeadMatch->team1_id;
            $losingTeam = Team::findOrFail($losingTeamId);

            if ($winningTeam) {
                $winningTeam->update([
                    'wins' => $winningTeam->wins + 1,
                    'points' => $winningTeam->points + 3
                ]);
            }

            if ($losingTeam) {
                $losingTeam->update([
                    'loses' => $losingTeam->loses + 1
                ]);
            }



            $users = User::all();
            foreach ($users as $user) {
                $tokens = $user->fcmTokens()->pluck('fcmToken')->toArray();
            }

            $title = 'Head-to-Head Match Ended';
            $body = 'A head-to-head match between ' . $winningTeam->teamName . ' VS ' . $losingTeam->teamName . ' has ended.
             Check out these teams records to view the match summary.';

            (new PushNotificationController)->sendNotification($tokens, $body, $title);

            
        }


        return response()->json([
            'code' => 200,
            'message' => 'Head-to-head match updated successfully.',
        ]);
    }



    public function deleteH2HMatch(String $id)
    {
        $head2HeadMatch = Head2HeadMatch::find($id);

        if (!$head2HeadMatch) {
            return response()->json([
                'code' => 404,
                'message' => 'Head-to-head match not found.',
            ], 404);
        }

        // Delete associated events first
        $head2HeadMatch->events()->delete();

        $images = $head2HeadMatch->images;
        foreach ($images as $image) {

            Storage::disk('public')->delete($image->path);
            $image->delete();

        }

        // Then delete the head2head match
        $head2HeadMatch->delete();

        return response()->json([
            'code' => 200,
            'message' => 'Head-to-head match and associated events deleted successfully.',
        ]);
    }
}
