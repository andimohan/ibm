<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('TemplateEMKLPurchaseItem.class.php'));
$templateEMKLPurchaseItem = new TemplateEMKLPurchaseItem();

$obj = $templateEMKLPurchaseItem;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'templateEMKLPurchaseItemForm';
 
$quickView = false;
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';


$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));  
array_push($arrSearchColumn, array('Item', $obj->tableName. '.name')); 

$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100));  
array_push($arrColumn, array(ucwords($obj->lang['name']),'name')); 
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',90));
  
 
function generateQuickView($obj,$id){ 
	
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
