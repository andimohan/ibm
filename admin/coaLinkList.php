<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('COALink.class.php');
$coaLink = createObjAndAddToCol(new COALink());

$obj = $coaLink;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
 
if(!$security->isAdminLogin($securityObject,10,true));
  
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>