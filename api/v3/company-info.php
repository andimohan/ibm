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

$postVars = json_decode($fileContent,true); 
$code = (isset($postVars['code'])) ? $postVars['code'] : '';

// normalnya
$arrAuth= explode('.',$_SERVER['HTTP_AUTHORIZATION']);
$userkey = $arrAuth[1];

$actionkey = 10;
switch($ACTION){ 
    case 'POST' :  
    case 'PUT' :  $actionkey = 11; break;
    case 'DELETE' : $actionkey = 12; break;   
    case 'CHANGESTATUS' : $actionkey = $changeStatusKey; break;   
};

$gatePassResponse = $security->APIGatePassV3($userkey, '', $actionkey,true);

/* RECEIVE VALUE */
$DBLicenseCon = $OBJ->masterConn();
$sql = 'select * from customer_company where code = '. $DBLicenseCon->paramString($code);

$rs = $DBLicenseCon->doQuery($sql);  
$DBLicenseCon = null; 
 
if (empty($rs)){  
    $RETURN_VALUE['response_code']  = 401;  
    $RETURN_VALUE['message'] = $OBJ->lang['noDataFound']; 
}else{  
    
    $RETURN_VALUE['response_code']  = 200;
    $RETURN_VALUE['message'] = '';  
    $RETURN_VALUE['data'] = array(
                                    'code' => $rs[0]['code'],
                                    'domain' => $rs[0]['name'],
                                    'category_id' => $rs[0]['categorykey'], // categorykey saja, kedepannya harusnya patokanyna categorgykey.
                                    // khusus customer lama nanti pengecualian saja
                                );  
}		
  

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;

?>