<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('Recruitment.class.php'));

$recruitment = createObjAndAddToCol( new Recruitment()); 

$obj = $recruitment;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'recruitmentForm';
$quickView = false;
 		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array($obj->lang['code'], $obj->tableName . '.code')); 
array_push($arrSearchColumn, array($obj->lang['name'], $obj->tableName . '.name')); 
array_push($arrSearchColumn, array($obj->lang['email'], $obj->tableName . '.email')); 
array_push($arrSearchColumn, array($obj->lang['phone'], $obj->tableName . '.phone')); 
array_push($arrSearchColumn, array($obj->lang['address'], $obj->tableName . '.address')); 
		  
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';  

function generateQuickView($obj,$id){ 
	$detail = '';
    
    return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
