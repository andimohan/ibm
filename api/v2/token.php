<?php
require_once '../../_config.php';  
require_once '_include.php';

$OBJ = $class; 

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
 if (!$OBJ->checkAPIAuth(getHeaders(),$fileContent)) endForInvalidTokenError();

/* RECEIVE VALUE */
$userName=$postVars['username'];
$password=$postVars['password']; 
  
$result = $security->getUserAuthOTP($userName,$password);
 
if ( empty($result)){  
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = $OBJ->errorMsg[300]; 
}else{   
    $RETURN_VALUE['response_code']  = 200;
    $RETURN_VALUE['message'] = '';  
    $RETURN_VALUE['data'] = $result;  
}		
  

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;

?>