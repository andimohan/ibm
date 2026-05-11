<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 
 

includeClass(array('AccountStatementImport.class.php'));
$accountStatementImportImport = createObjAndAddToCol( new AccountStatementImport()); 

$obj = $accountStatementImportImport;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
	 									// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'accountStatementImportForm';

function generateQuickView($obj,$id){ 
	 
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>