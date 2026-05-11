<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 
includeClass('EMKLJobOrderHeader.class.php');
$emklJobOrderHeaderImport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['import']));

//$emklJobOrderImport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['import']));
//$emklPurchaseOrderImport = createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['import']));
//$consignee = createObjAndAddToCol(new Consignee());
//$item = createObjAndAddToCol(new Item());
//$supplier = createObjAndAddToCol(new Supplier());

$quickView = false;
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

$obj = $emklJobOrderHeaderImport; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
    
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'emklJobOrderImportHeaderForm';

 
function generateQuickView($obj,$id){ 
    $detail = '';
	
    return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>