<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

$ACTION = $_SERVER['REQUEST_METHOD'];    
$_RESPONSE = json_decode(file_get_contents("php://input"),true);   
setLog($_RESPONSE);

if(empty($_RESPONSE)) die;
  
die;

function setLog($msg){  
    
    if(is_array($msg)) $msg = print_r($msg, true);
    error_log ($msg.chr(13),3,'../../log/manual-log-lz');
}


?>