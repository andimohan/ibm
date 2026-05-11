<?php
require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/LoginLog.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Marketplace.class.php';  // utl login lazada, datamoat

$OBJ = $class; 
$loginLog =  new LoginLog() ; 

$API_FIELDS = array_merge($API_FIELDS,array(
               'username' =>   array('paramName' => 'username', 'mandatory' => true), 
               'password'  =>  array('paramName' => 'password', 'mandatory' => true) 
            ));
       


// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access"); 
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
$ACTION = $_SERVER['REQUEST_METHOD'];  

if($ACTION != 'POST') endForRequestMethodError();

$RETURN_VALUE = array();

// POST / PUT 
$fileContent = file_get_contents("php://input");
parse_str($fileContent,$postVars);
  
// check token  
function getHeaders() {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
    }
    return $headers; 
} 

// check token  
 if (!$OBJ->checkAPIAuth(getHeaders(),$fileContent))  endForInvalidTokenError();


/* RECEIVE VALUE */
$userName=$postVars['username'];
$password=$postVars['password']; 
$token = (!empty($postVars['authcode'])) ? $postVars['authcode'] : '';

//$deviceFingerprint = (!empty($_POST['df'])) ? $_POST['df'] : '';
  
if ($loginLog->isLockout($userName,2)){ 
        $lockoutMinutes =  ceil($class->loadSetting('lockoutSecond') / 60); 
        $errorMsg = $class->errorMsg['login'][3];

        $patterns = array();
        $patterns[count($patterns)] = '/({{LOCKOUT_MINUTES}})/'; 

        $replacement = array();
        $replacement[count($replacement)] =$lockoutMinutes; 

        $errorMsg = preg_replace($patterns, $replacement, $errorMsg);  
     
        $RETURN_VALUE['response_code']  = 401;  
        $RETURN_VALUE['message'] = $errorMsg;  
        http_response_code($RETURN_VALUE['response_code'] ); 

        echo json_encode($RETURN_VALUE); 
        die;
} 


$arrLoginLog = array();
$arrLoginLog['logintype'] = 2;
$arrLoginLog['username'] = $userName;
$arrLoginLog['statuskey'] = 2;  
$arrLoginLog['userkey'] = 0 ;  

  
$result = $security->adminLogin($userName,$password,$token);
 
if ( $result['valid'] == false){  
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = $result['message'];  
}else{ 

    $arrLoginLog['statuskey'] = 1 ;  
    $arrLoginLog['userkey'] = $result['data']['pkey'] ;  
    
    $RETURN_VALUE['response_code']  = 200;
    $RETURN_VALUE['message'] = '';  
    $RETURN_VALUE['data'] = array(
                                'pkey' => $result['data']['pkey'],
                                'code' => $result['data']['code'],
                                'user_real_name' => $result['data']['name'],
                                'username' => $result['data']['username'],
                                'password' => $result['data']['password']
                            );  
}		
 
$loginLog->addData($arrLoginLog);  

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;

?>