<?php    

require_once '../_config.php'; 
require_once '../_include.php';  

$obj = $carRevenue;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'carRevenueForm';
$quickView = false;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name'));
array_push($arrSearchColumn, array(ucwords($obj->lang['reference']), $obj->tableName. '.refcode'));
array_push($arrSearchColumn, array(ucwords($obj->lang['customer']), $obj->tableCustomer. '.name'));
array_push($arrSearchColumn, array(ucwords($obj->lang['driver']), $obj->tableEmployee. '.name'));
array_push($arrSearchColumn, array(ucwords($obj->lang['car']), $obj->tableCar. '.policenumber'));
array_push($arrSearchColumn, array('Jumlah', $obj->tableName. '.amount'));  
 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';  

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
