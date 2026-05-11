<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Location.class.php');
$location = createObjAndAddToCol(new Location()); 


$obj = $location;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'locationForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama Lokasi', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Nama Lokasi', $obj->tableCity . '.name'));
array_push($arrSearchColumn, array('Nama Lokasi', $obj->tableCityCategory . '.name'));
array_push($arrSearchColumn, array('Status', $obj->tableName . '.statuskey'));
  
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>
