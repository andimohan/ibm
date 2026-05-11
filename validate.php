<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';
require_once '_global.php';

$module = $_GET['module'];
$token = $_GET['token'];

$valid = false;
$arrData = array();
$arrResult = array();

if(empty($module) || empty($token)){ 
	$valid = false;
} else {
	
	$obj = null;
	$arrStatus = array(2,3);
		
	switch ($obj){
		case 'salesorder': includeClass(array("SalesOrder.class.php")); 
						  $obj = new SalesOrder();
						  break;
		default:   includeClass(array("SalesOrder.class.php")); 
						  $obj = new SalesOrder();
						  break;
	}
	
	// sementara split 6 terakhir utk pin, sisanya pkey

	// Get last 6 characters
	$pinLength = 6;
	
	$pinLength *= -1;
	$pin = substr($token, $pinLength); 
	$transactionkey = $obj->convertNumAlpha(substr($token, 0, $pinLength)); 
	$result = [$transactionkey, $pin];
	
	$rs = $obj->searchData($obj->tableName.'.pkey', $transactionkey, true, ' and '.$obj->tableName.'.statuskey in ('.$obj->oDbCon->paramString($arrStatus,', ').')');
	if(!empty($rs) && substr($rs[0]['checksum'], $pinLength) == $pin){ 
		$valid = true;
		$rs[0]['detail'] = $obj->getDetailWithRelatedInformation($rs[0]['pkey']);
		$arrData = $rs;
		
	}
	 
	$arrResult = array(
		'valid' => $valid,
		'data' => $arrData
	);
}






$arrTwigVar ['arrResult'] = $arrResult;  

echo $twig->render('validate.html', $arrTwigVar);
?>