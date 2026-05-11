<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('CustomCode.class.php'));

$customCode = new CustomCode();
$obj = $customCode;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'customCodeForm';
$quickView = false;


$arrSearchColumn = array ();
array_push($arrSearchColumn, array(ucwords($obj->lang['code']), $obj->tableName . '.code'));
array_push($arrSearchColumn, array(ucwords($obj->lang['name']), $obj->tableName . '.name')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['title']), $obj->tableName . '.title')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['format']), $obj->tableName . '.codeformat')); 
array_push($arrSearchColumn, array(ucwords($obj->lang['category']), $obj->tableCode . '.label')); 
 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';
$overwriteContextMenu['print'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>