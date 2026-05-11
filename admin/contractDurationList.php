<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $contractDuration;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'contractDurationForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
	
$arrColumn = array ();
array_push($arrColumn, array('Kode','code',120));
array_push($arrColumn, array('Nama Kontrak','name'));
array_push($arrColumn, array('Bunga (%)','interest',100,'right','decimal'));
array_push($arrColumn, array('Periode','interestmaturityname',100));
array_push($arrColumn, array('Denda','fine',100,'right','integer'));
array_push($arrColumn, array('Periode','finematurityname',100));
array_push($arrColumn, array('Jatuh Tempo (Hari)','duedays',100,'right','integer'));
array_push($arrColumn, array('Status','statusname',70));
  
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';  

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>