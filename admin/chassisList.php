<?php  
// ========================================================================== INITIALIZE ==========================================================================

include '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Chassis.class.php');
$chassis = createObjAndAddToCol(new Chassis()); 


$obj = $chassis;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'chassisForm';
$quickView = false;
  
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nomor Chassis', $obj->tableName . '.chassisnumber'));
array_push($arrSearchColumn, array('Kategori Chassis', $obj->tableCategory . '.name'));

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>