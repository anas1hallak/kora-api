<?php

namespace App\Http\Controllers;

use App\Models\Head2HeadMatch;
use App\Models\Head2HeadRequest;
use Illuminate\Http\Request;

class Head2HeadRequestsController extends Controller
{
    


    public function submitH2Hrequest(string $id){


        $Head2HeadMatch=Head2HeadMatch::findOrFail($id);

        if (!is_null($Head2HeadMatch->ibanNumber1) && !is_null($Head2HeadMatch->ibanNumber2)) {


            $head2HeadRequest = Head2HeadRequest::create([

                'Head2HeadMatch_id' => $Head2HeadMatch->id,
                'team1_id' => $Head2HeadMatch->team1_id,
                'team2_id' => $Head2HeadMatch->team2_id,
                'ibanNumber1' => $Head2HeadMatch->ibanNumber1,
                'ibanNumber2' => $Head2HeadMatch->ibanNumber2,

            ]);


            $Head2HeadMatch->update([

            
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





}
