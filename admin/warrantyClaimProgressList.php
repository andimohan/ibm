<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $warrantyClaimProgress;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'warrantyClaimProgressForm'; 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer . '.name'));
array_push($arrSearchColumn, array('Kode Klaim', $obj->tableWarrantyClaim . '.code'));  
array_push($arrSearchColumn, array($obj->lang['serialNumber'], $obj->tableName . '.serialnumber'));  
array_push($arrSearchColumn, array($obj->lang['itemName'], 'claimitem.name'));  
array_push($arrSearchColumn, array($obj->lang['itemName'], 'newitem.name'));  
array_push($arrSearchColumn, array('Catatan', $obj->tableName . '.trdesc'));


function generateQuickView($obj,$id){ 

	//$rsDetail = $obj->getDetailWithRelatedInformation($id);
	    
	$detail = '';
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>