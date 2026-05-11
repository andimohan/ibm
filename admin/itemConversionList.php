<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('ItemConversion.class.php');
$itemConversion = createObjAndAddToCol( new ItemConversion());  

$obj = $itemConversion;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'itemConversionForm';
$quickView = false;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate'));
array_push($arrSearchColumn, array('Kategori', $obj->tableItemCategory. '.name'));
array_push($arrSearchColumn, array('Brand', $obj->tableBrand. '.name'));

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
 
	$detail = ''; 	
							
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
