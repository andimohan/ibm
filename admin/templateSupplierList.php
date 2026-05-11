<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('TemplateSupplier.class.php'));
$templateSupplier = new TemplateSupplier();

$obj = $templateSupplier;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'templateSupplierForm';
$quickView = false;

 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 


function generateQuickView($obj,$id){ 
	$detail = '';
	 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
