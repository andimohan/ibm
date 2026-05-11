<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass(array('DisposalWorkOrderDispatcher.class.php'));
$disposalWorkOrderDispatcher = createObjAndAddToCol(new DisposalWorkOrderDispatcher());
$disposalJobOrder = createObjAndAddToCol(new DisposalJobOrder());

$obj = $disposalWorkOrderDispatcher;

$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'disposalWorkOrderDispatcherForm';

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');

?>