<?php
require_once '../../_config.php';  
require_once '_include.php';
  

$OBJ = $class;  

$API_FIELDS = array_merge($API_FIELDS,array(
               'code' =>   array('paramName' => 'code', 'mandatory' => true),  
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
 

/* RECEIVE VALUE */ 
$DBLicenseCon = $OBJ->masterConn();
$sql = 'select * from customer_company where code = '. $DBLicenseCon->paramString($postVars['code']); 
$rs = $DBLicenseCon->doQuery($sql);  
$DBLicenseCon = null; 
 
if (empty($rs)){  
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = $OBJ->errorMsg['noDataFound']; 
}else{  
    
    $RETURN_VALUE['response_code']  = 200;
    $RETURN_VALUE['message'] = '';  
    $RETURN_VALUE['data'] = array(
                                    'code' => $rs[0]['code'],
                                    'domain' => $rs[0]['name'],   
                                    //'token' => $rs[0]['secretkey'],                          
                                );  
}		
  

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;

?>