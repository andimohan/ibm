<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $paymentConfirmation;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'paymentConfirmationForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Invoice',  $obj->tableSales.'.code'));
array_push($arrSearchColumn, array('Bank Name', $obj->tableName . '.bankname'));
array_push($arrSearchColumn, array('Bank Acc. Number', $obj->tableName . '.bankaccountnumber'));
array_push($arrSearchColumn, array('Bank Acc. Name', $obj->tableName . '.bankaccountname'));
array_push($arrSearchColumn, array('Amount', $obj->tableName . '.amount'));

 
function generateQuickView($obj,$id){ 
   
 	$detail  = '';	
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>