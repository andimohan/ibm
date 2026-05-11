<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $truckingPurchaseOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'truckingPurchaseOrderForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Supplier', $obj->tableName. '.suppliername')); 
array_push($arrSearchColumn, array('Kode So', $obj->tableName. '.socode')); 
array_push($arrSearchColumn, array('Total', $obj->tableName. '.grandtotal'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));

$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100));
array_push($arrColumn, array(ucwords($obj->lang['date']),'trdate',100,'center','date'));  
array_push($arrColumn, array(ucwords($obj->lang['supplier']),'suppliername')); 
array_push($arrColumn, array(ucwords($obj->lang['soCode']),'socode', 120));   
array_push($arrColumn, array(ucwords($obj->lang['total']),'grandtotal',80,'right','integer')); 
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',90));
 
$printTransactionFunction = $class->generatePrintContextMenu('print','printTruckingPurchaseOrder');

$overwriteContextMenu["printSeparator"] = "-";
$overwriteContextMenu["print"] = array("name" => $obj->lang['printTransaction'],"icon" =>"print","callbackFunction" => $printTransactionFunction);
  
 
function generateQuickView($obj,$id){ 
	
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
