<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Traits\Firebase;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class PushNotificationController extends Controller
{
   use Firebase;

    
    public function sendNotification($tokens,$body,$title){


        $fcmUrl="https://fcm.googleapis.com/v1/projects/kora-project-63c71/messages:send";
        $AuthToken=$this->getGoogleAccessToken();



        if (!is_array($tokens) || empty($tokens)) {
            return response()->json([
                'error' => 'Invalid or empty FCM tokens array.',
            ], 400);
        }



        foreach ($tokens as $token) {

            $fcmNotification = [
                'message' => [
                    'token' => $token,
                    'notification' => [
                        'body' => $body,
                        'title' => $title,
                    ],
                ],
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $AuthToken,
            ])->post($fcmUrl, $fcmNotification);

           
           
        }

        return;
    }
}