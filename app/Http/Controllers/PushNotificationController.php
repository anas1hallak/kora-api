<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Traits\Firebase;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Http\Request;


class PushNotificationController extends Controller
{
   use Firebase;

    
    public function sendNotification(Request $request){
        $token=$request->input('fcmToken');
        $notification = [
            'title' =>'title',
            'body' => 'body of message.',
            'icon' =>'myIcon',
            'sound' => 'mySound'
        ];
        $extraNotificationData = ["message" => $notification,"moredata" =>'dd'];

        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to'        => $token, //single token
            'notification' => $notification,
            'data' => $extraNotificationData
        ];
        
        return $this->firebaseNotification($fcmNotification); 

    }

}
