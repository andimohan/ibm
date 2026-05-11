<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('DisposalWorkOrder.class.php');
$disposalWorkOrder = createObjAndAddToCol(new DisposalWorkOrder());

$obj = $disposalWorkOrder;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'disposalWorkOrderForm';

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');

?>