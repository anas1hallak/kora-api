<?php


namespace App\Traits;


trait Firebase
{

    private function getGoogleAccessToken(){
        $credentialsFilePath = base_path('kora-project-63c71-firebase-adminsdk-z4qo5-5db5cf1627.json');
        $client = new \Google_Client();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return $token['access_token'];
   }



}