<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $bug;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'bugForm';
$quickView = false;
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array(ucwords($obj->lang['code']), $obj->tableName . '.code')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['subject']), $obj->tableName . '.subject'));
 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>