<?php
require_once '../../_config.php';  
require_once '_include.php'; 
   
$OBJ = $class;  
 

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access"); 
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
$ACTION = $_SERVER['REQUEST_METHOD'];  

if($ACTION != 'GET') endForRequestMethodError();

$RETURN_VALUE = array();

// POST / PUT 
$fileContent = file_get_contents("php://input");
parse_str($fileContent,$postVars);
 

/* RECEIVE VALUE */ 
$companyName = $class->loadSetting('companyName');
$profileImg = $class->loadSetting('companyLogo'); 

$avatarImg = '';
if (!empty($profileImg)) 
    $avatarImg = HTTP_HOST.'phpthumb/phpThumb.php?src='.$class->phpThumbURLSrc .'setting/companyLogo/'.$profileImg.'&far=C&f=png&hash='.getPHPThumbHash($profileImg); 
else
    $avatarImg = HTTP_HOST.'include/img/avatar-default.jpg';

$RETURN_VALUE['response_code']  = 200; 
$RETURN_VALUE['data'] = array(
                            'company_name' => $companyName,   
                            'company_logo_url' => $avatarImg,                      
                        );  	


http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;

?>