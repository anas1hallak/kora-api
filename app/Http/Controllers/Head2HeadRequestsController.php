<?php

namespace App\Http\Controllers;

use App\Models\Head2HeadMatch;
use App\Models\Head2HeadRequest;
use Illuminate\Http\Request;

class Head2HeadRequestsController extends Controller
{
    



    public function getALLH2HRequests()
    {
        $head2HeadRequests = Head2HeadRequest::with(['team1', 'team2' ,'H2HMatch'])->get();;

        return response()->json([
            'code' => 200,
            'head2HeadRequests' => $head2HeadRequests,
        ]);
    }




    public function submitH2Hrequest(string $id){


        $head2HeadMatch=Head2HeadMatch::findOrFail($id);

        if (!is_null($head2HeadMatch->ibanNumber1) && !is_null($head2HeadMatch->ibanNumber2)) {


            $head2HeadRequest = Head2HeadRequest::create([

                'Head2HeadMatch_id' => $head2HeadMatch->id,
                'team1_id' => $head2HeadMatch->team1_id,
                'team2_id' => $head2HeadMatch->team2_id,
                'ibanNumber1' => $head2HeadMatch->ibanNumber1,
                'ibanNumber2' => $head2HeadMatch->ibanNumber2,

            ]);


            $head2HeadMatch->update([

            
                'status' =>"pending_approval",
            
                
            ]);
    
    
            return response()->json([
                'code' => 200,
                'message' => 'H2H request submitted successfully',
            ]);
        }
        
        else {

            return;
        
        }


    }



    public function acceptH2HRequest(String $id){

        $head2HeadRequest = Head2HeadRequest::findOrFail($id);
    
        $head2HeadMatch = Head2HeadMatch::findOrFail($head2HeadRequest->Head2HeadMatch_id);
        
        $head2HeadRequest->delete();
    
        $head2HeadMatch->update([
            'status' => "approved",
        ]);
    
        return response()->json([
            'code' => 200,
            'message' => 'Request approved successfully',
        ]);



    }


    public function rejectH2HRequest(String $id){

        $head2HeadRequest = Head2HeadRequest::findOrFail($id);
    
        $head2HeadMatch = Head2HeadMatch::findOrFail($head2HeadRequest->Head2HeadMatch_id);


        $head2HeadRequest->delete();
        $head2HeadMatch->delete();



        return response()->json([

            'code'=>200,
            'message' => 'request rejected successfully',
        
        ]);



    }



}
