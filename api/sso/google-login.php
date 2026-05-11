<?php

define(SSO_DOMAIN, 'google'); 
require_once 'login.php';


function attemptAuth($clientId){
    $resArr = array('message'=> "", 'valid' => false, 'data' => "{}");
 
    // Google's Token Verification
    $isCSRFSafe = true;

    if (!array_key_exists('g_csrf_token', $_COOKIE)){
        $resArr['message'] = "ERROR 404 - No CSRF token in cookie!";
        $isCSRFSafe = false;
    }
    if ($isCSRFSafe && !array_key_exists('g_csrf_token', $_POST)){
        $resArr['message'] = "ERROR 400 - No CSRF token in POST body!";
        $isCSRFSafe = false;
    }
    if ($isCSRFSafe && $_COOKIE['g_csrf_token'] != $_POST['g_csrf_token']){
        $resArr['message'] = "ERROR 400 - FAILED to verify double submit cookie!";
        $isCSRFSafe = false;
    }

    if ($isCSRFSafe){
        $postCreds = $_POST['credential'];

        if (!empty($clientId)){
            $client = new Google_Client(['client_id' => $clientId]);
            $payload = $client->verifyIdToken($postCreds);
            if ($payload){ 
                $resArr['valid'] = true;
                $resArr['message'] = "Authentication valid !";
                $resArr['data'] = $payload;
            }
            else{
                $resArr['message'] = "ERROR 400 - Invalid ID token!";
            }
        }
    }


    return json_encode($resArr, JSON_FORCE_OBJECT);
}

?>