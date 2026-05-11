<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $itemDepot;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'itemDepotForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory. '.name') );
 
$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']) ,'code',100));
array_push($arrColumn, array(ucwords($obj->lang['name']),'name')); 
array_push($arrColumn, array(ucwords($obj->lang['category']),'categoryname',150));
array_push($arrColumn, array(ucwords($obj->lang['qty']),'qtyonhand',70 ,'right','integer'));
array_push($arrColumn, array(ucwords($obj->lang['unit']),'baseunitname',70));
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
		 

function generateQuickView($obj,$id){ 
 
	$rs = $obj->getDataRowById($id);   
	
	$detail = '';
	  
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>