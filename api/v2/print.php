<?php

/*$fileContent = file_get_contents("php://input");
parse_str($fileContent,$postVars);*/

// sementara AUTH dari GET
if (!isset($_GET['auth']) || empty($_GET['auth'])) die;
if(isset($_GET['auth']) && !empty($_GET['auth'])){
   
    $postVars = array();
    
    $postVars['id'] = $_GET['id'];
    $postVars['file_name'] = $_GET['file_name']; 
    $postVars['userkey'] = $_GET['_userkey']; 
    $postVars['auth'] = $_GET['auth']; 
    $fileContent = '';
      
}

$_GET['filename'] = (isset($postVars['file_name']) && !empty($postVars['file_name']) ) ? $postVars['file_name'] : '';
$_GET['id'] = (isset($postVars['id']) && !empty($postVars['id']) ) ? $postVars['id'] : '';

// lihat logol
// harus di letakan disini

die;

if(isset($postVars) && isset($fileContent)){ 
    $userkey = (isset($postVars['userkey']) && !empty($postVars['userkey']) ) ? $postVars['userkey'] : 0; 
    $security->APIGatePass($userkey, $fileContent,$securityObject, 10,true, $postVars['auth']);  
}

require_once $_SERVER ['DOCUMENT_ROOT'].'/admin/print/print.php'; 
      
?>