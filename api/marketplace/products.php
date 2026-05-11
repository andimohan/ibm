<?php
require_once '../../_config.php';  
require_once '_include.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Marketplace.class.php';  

// required headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access"); 
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
$ACTION = $_SERVER['REQUEST_METHOD'];  

//if($ACTION != 'POST') endForRequestMethodError(); 

$RETURN_VALUE = array();

// POST / PUT  
$fileContent = file_get_contents("php://input");
/*
if($security->isJSON($fileContent))
    $postVars = json_decode($fileContent,true);
else
    parse_str($fileContent,$postVars); 
*/

 $postVars = json_decode($fileContent,true);

parse_str($fileContent,$postVars);
$postVars = json_decode($fileContent,true);
 
$class->setLog('$postVars',true);
$class->setLog($postVars,true);

$arrUpdate = array();
foreach($postVars as $row){ 
    array_push($arrUpdate, array('sku' => $row['id'], 'new_stock' => intval($row['qty']) ));
}

$class->setLog('$arrUpdate',true);
$class->setLog($arrUpdate,true);

$tokopedia = new Tokopedia();
$url = $tokopedia->url . 'inventory/v1/fs/'.$tokopedia->fsid.'/stock/update?shop_id='.$tokopedia->shopId;
$RETURN_VALUE = $tokopedia->execute($url,'POST', $arrUpdate); 

$class->setLog($RETURN_VALUE,true);

http_response_code($RETURN_VALUE['response_code']); 
echo json_encode($RETURN_VALUE); 
die;

?>