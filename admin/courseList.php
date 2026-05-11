<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include.php'; 


$obj = $course;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'courseForm';
 
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['name'], $obj->tableName . '.name')); 
array_push($arrSearchColumn, array($obj->lang['warehouse'], $obj->tableWarehouse . '.name')); 

function generateQuickView($obj,$id){ 
	$detail = '';
    
    return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
