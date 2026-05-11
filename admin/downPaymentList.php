<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $downPayment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'downPaymentForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Job Order', $obj->tableJobOrder. '.code')); 
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name')); 
array_push($arrSearchColumn, array('Total', $obj->tableName. '.amount')); 
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));

$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',80));
array_push($arrColumn, array(ucwords($obj->lang['date']),'trdate',90,'center','date')); 
array_push($arrColumn, array(ucwords($obj->lang['jobOrder']),'refCode',90)); 
array_push($arrColumn, array(ucwords($obj->lang['customer']),'customername'));  
array_push($arrColumn, array(ucwords($obj->lang['amount']),'amount', 100,'right','integer'));
array_push($arrColumn, array(ucwords($obj->lang['description']),'trdesc',300));  
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
  


// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
