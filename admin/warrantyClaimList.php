<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $warrantyClaim;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'warrantyClaimForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name')); 
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));
 
function generateQuickView($obj,$id){  
	$detail = '';
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
