<?php  
// ========================================================================== INITIALIZE ==========================================================================

include '../_config.php'; 
include '../_include.php'; 


$obj = $carMaintenanceChecklist;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'carMaintenanceChecklistForm';
$quickView = false;
  
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nomor Polisi', $obj->tableName . '.policenumber')); 
array_push($arrSearchColumn, array('Series', $obj->tableName . '.seriesname')); 
 

$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100));
array_push($arrColumn, array(ucwords($obj->lang['carRegistrationNumber']),'policenumber')); 
array_push($arrColumn, array(ucwords($obj->lang['series']),'seriesname',150));
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
 

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}

$printTransactionFunction = $class->generatePrintContextMenu('print','printCarMaintenanceCheckList');   
			    
$overwriteContextMenu["printSeparator"] = "-";
$overwriteContextMenu["print"] = array("name" => $obj->lang['print'],"icon" =>"print","callbackFunction" => $printTransactionFunction);
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>