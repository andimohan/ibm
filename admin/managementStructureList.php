<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('ManagementStructure.class.php'));
$managementStructure = createObjAndAddToCol( new ManagementStructure()); 

$obj = $managementStructure;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'managementStructureForm';
$quickView = false;

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';
$overwriteContextMenu['print'] = ''; 

function generateQuickView($obj,$id){  
	$detail = ''; 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>