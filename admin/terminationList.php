<?php  
// ========================================================================== INITIALIZE ==========================================================================
include '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Termination.class.php');   
$termination = createObjAndAddToCol( new Termination()); 


$obj = $termination;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'terminationForm';
 
		
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code')); 
array_push($arrSearchColumn, array('Kode SC', $obj->tableSalesOrder . '.code')); 
array_push($arrSearchColumn, array('Pelangan', $obj->tableCustomer . '.name')); 
array_push($arrSearchColumn, array('SID', $obj->tableCustomer . '.sid')); 
    
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
