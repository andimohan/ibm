<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass(array('Marketplace.class.php')); 
$marketplace = createObjAndAddToCol( new Marketplace()); 

$obj = $marketplace;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'marketplaceForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name')); 
array_push($arrSearchColumn, array('Default Pelanggan', $obj->tableCustomer . '.name')); 
 
$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100));
array_push($arrColumn, array(ucwords($obj->lang['name']),'name'));  
array_push($arrColumn, array(ucwords($obj->lang['customer']),'customername',200));     
array_push($arrColumn, array(ucwords($obj->lang['description']),'trdesc',300));     
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
		   
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 


function generateQuickView($obj,$id){  
	$detail = ''; 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>