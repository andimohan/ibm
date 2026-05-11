<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Storefront.class.php');
$storefront = createObjAndAddToCol(new Storefront());

$obj = $storefront;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'storefrontForm';
$quickView = false;
 
$arrSearchColumn = array ();
//array_push($arrSearchColumn, array('Etalase ID', $obj->tableName . '.storeid'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Nama Marketplace', $obj->tableMarketplace . '.name'));
 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';
$overwriteContextMenu['print'] = ''; 

function generateQuickView($obj,$id){  
	$detail = ''; 
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>