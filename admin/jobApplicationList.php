<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass(array('JobApplication.class.php'));

$jobApplication = createObjAndAddToCol( new JobApplication()); 

$obj = $jobApplication;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'jobApplicationForm';
$quickView = false;  
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';  

function generateQuickView($obj,$id){ 
	$detail = '';
    
    return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>