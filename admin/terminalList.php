<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass('Terminal.class.php');
$terminal = createObjAndAddToCol(new Terminal()); 

$obj = $terminal;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'terminalForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name')); 
array_push($arrSearchColumn, array('Lokasi', $obj->tableCity . '.name')); 
array_push($arrSearchColumn, array('Kategori Lokasi', $obj->tableCityCategory . '.name')); 
 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>
