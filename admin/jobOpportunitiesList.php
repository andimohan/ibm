<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('JobOpportunities.class.php'));

$jobOpportunities = createObjAndAddToCol( new JobOpportunities()); 


$obj = $jobOpportunities;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'jobOpportunitiesForm';
$quickView = false; 

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';
$overwriteContextMenu['print'] = ''; 

function generateQuickView($obj,$id){ 
	return '';  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>