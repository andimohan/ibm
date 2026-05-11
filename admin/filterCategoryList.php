<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 


$obj = $filterCategory;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'filterCategoryForm';
$quickView = false;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama Kategori', $obj->tableName . '.name'));
 
$arrColumn = array ();
array_push($arrColumn, array('Kode','code',100));
array_push($arrColumn, array('Nama Kategori','name'));
array_push($arrColumn, array('Status','statusname',70));

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>