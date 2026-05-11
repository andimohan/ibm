<?php

define(SSO_DOMAIN, 'facebook'); 
require_once 'login.php';


function attemptAuth($clientId,$secretKey){
        $resArr = array('message'=> "", 'valid' => false, 'data' => "{}");
 
        $fb = new Facebook\Facebook([
            'app_id' => $clientId,
            'app_secret' => $secretKey,
            'default_graph_version' => 'v11.0',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        $tokenIsValid = true;
        try {
            $accessToken = $helper->getAccessToken();
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $resArr['message'] = "ERROR 400 - Graph returned an error: " . $e->getMessage();
            $tokenIsValid = false;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            $resArr['message'] = "ERROR 400 - Facebook SDK returned an error: " . $e->getMessage();
            $tokenIsValid = false;
        }

        if ($tokenIsValid){
            // The OAuth 2.0 client handler helps us manage access tokens
            $oAuth2Client = $fb->getOAuth2Client();

            // Get the access token metadata from /debug_token
            $tokenMetadata = $oAuth2Client->debugToken($accessToken);

            // Validation (these will throw FacebookSDKException's when they fail)
            $tokenMetadata->validateAppId($clientId);
            // If you know the user ID this access token belongs to, you can validate it here
            //$tokenMetadata->validateUserId('123');
            $tokenMetadata->validateExpiration();

            $tokenIsLongLived = true;
            if (!$accessToken->isLongLived()) {
                // Exchanges a short-lived access token for a long-lived one
                try {
                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                    $resArr['message'] = "ERROR 400 - Error getting long-lived access token: " . $e->getMessage();
                    $tokenIsLongLived = false;
                }
            }

            if($tokenIsLongLived){
                // User is logged in with a long-lived access token.
                $_SESSION['fb_access_token'] = (string) $accessToken;

                // Getting User's Profile
                $profileGetIsSuccess = true;
                try {
                    // Returns a `Facebook\FacebookResponse` object
                    $response = $fb->get('/me?fields=id,email,first_name,middle_name,last_name,short_name,picture', $accessToken);
                } catch(Facebook\Exceptions\FacebookResponseException $e) {
                    $resArr['message'] = "ERROR 400 - Graph returned an error: " . $e->getMessage();
                    $profileGetIsSuccess = false;
                } catch(Facebook\Exceptions\FacebookSDKException $e) {
                    $resArr['message'] = "ERROR 400 - Facebook SDK returned an error: " . $e->getMessage();
                    $profileGetIsSuccess = false;
                }

                if ($profileGetIsSuccess){
                    $user = $response->getGraphUser();
                    $userInfoArr = array();
                    $userInfoFieldNames = $user->getFieldNames();
                    unset($userInfoFieldNames[count($userInfoFieldNames)-1]);
                    foreach($userInfoFieldNames as $val) $userInfoArr[$val] = $user->getField($val);
                    $userInfoArr['picture'] = $user->getField('picture')->getUrl();

                    // overwrite name / others attribute
                    $arrName = array();
                    if(!empty($userInfoArr['first_name'])) array_push($arrName ,$userInfoArr['first_name']);
                    if(!empty($userInfoArr['middle_name'])) array_push($arrName ,$userInfoArr['middle_name']);
                    if(!empty($userInfoArr['last_name'])) array_push($arrName ,$userInfoArr['last_name']); 
                    $userInfoArr['name'] = implode(' ',$arrName);
                    
                    $userInfoArr['sub'] = time().rand(0,26);
                         
                    $resArr['message'] = "Authentication valid !";
                    $resArr['valid'] = true;
                    $resArr['data'] = $userInfoArr;
                }
            }
        }

    return json_encode($resArr, JSON_FORCE_OBJECT);
}

?>