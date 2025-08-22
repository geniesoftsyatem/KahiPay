<?php

namespace App\Libraries;

use Google_Client;

class Notifications
{
    private $android_google_url;
    private $service_account_file;

    public function __construct()
    {
        $this->service_account_file = WRITEPATH . 'credentials/service-account-file.json';
        $this->android_google_url = "https://fcm.googleapis.com/v1/projects/citycode-a59b2/messages:send";
    }

    private function getAccessToken()
    {
        $client = new Google_Client();
        $client->setAuthConfig($this->service_account_file);
        $client->addScope("https://www.googleapis.com/auth/firebase.messaging");
        $token = $client->fetchAccessTokenWithAssertion();
        return $token['access_token'];
    }

    public function sendAndroidNotification($fcm_reg_id = "", $message = "", $title = "")
    {
        $accessToken = $this->getAccessToken();

        // Prepare notification object
        $fcmNotification = array(
            'title' => $title,
            'body' => $message,
        );

        // Prepare data object
        $fcmData = array(
            'sound' => "default",
            'color' => "#203E78",
            'priority' => 'high',
            'message' => $message, // Send message in data
        );

        $fcmFields = array(
            'message' => array(
                'token' => $fcm_reg_id,
                'notification' => $fcmNotification,
                'data' => $fcmData, // Include data fields here
            ),
        );

        $headers = array(
            'Authorization: Bearer ' . $accessToken,
            'Content-Type: application/json'
        );

        if (extension_loaded('curl')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->android_google_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmFields));
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpcode !== 200) {
                return $result;
            }

            return $httpcode;
        }
    }
}
