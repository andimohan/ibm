<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
include '../_include-v2.php'; 

includeClass('InvoiceTax.class.php');   
$invoiceTax = createObjAndAddToCol( new InvoiceTax()); 

$obj = $invoiceTax;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'invoiceTaxForm';

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
