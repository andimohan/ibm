<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include.php'; 


$obj = $routineCost;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'routineCostForm';
$quickView = false;

 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code')); 
array_push($arrSearchColumn, array('Pemasok', $obj->tableSupplier . '.name')); 
array_push($arrSearchColumn, array('Deskripsi', $obj->tableName . '.trdesc')); 

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 
 
function generateQuickView($obj,$id){ 
	$detail = '';
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
