<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $changeItemSN;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'changeItemSNForm'; 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code'));  
array_push($arrSearchColumn, array($obj->lang['serialNumber'], $obj->tableName . '.serialnumber'));  
array_push($arrSearchColumn, array($obj->lang['itemName'], $obj->tableItem . '.name'));  
array_push($arrSearchColumn, array($obj->lang['vendorPartNumber'], $obj->tableVendorPartNumber. '.partnumber'));  
array_push($arrSearchColumn, array($obj->lang['serialNumber'], $obj->tableName. '.serialNumber'));  
array_push($arrSearchColumn, array($obj->lang['serialNumber'], $obj->tableName. '.newserialnumber'));  
array_push($arrSearchColumn, array('Catatan', $obj->tableName . '.trdesc'));

function generateQuickView($obj,$id){ 

	//$rsDetail = $obj->getDetailWithRelatedInformation($id);
	    
	$detail = '';
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>