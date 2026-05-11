<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('EMKLJobOrderHeader.class.php');
$emklJobOrderHeaderExport = createObjAndAddToCol(new EMKLJobOrderHeader(EMKL['jobType']['export']));
//$emklJobOrderExport = createObjAndAddToCol(new EMKLJobOrder(EMKL['jobType']['export']));
//$emklPurchaseOrderExport = createObjAndAddToCol(new EMKLPurchaseOrder(EMKL['jobType']['export']));

$quickView = false;
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

$obj = $emklJobOrderHeaderExport; 
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
  
    
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'emklJobOrderExportHeaderForm';
 
function generateQuickView($obj,$id){ 
    $detail = '';
	
    return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
