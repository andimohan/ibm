<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $youtube;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'youtubeForm';
$quickView = false;
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Judul', $obj->tableName . '.title'));
array_push($arrSearchColumn, array('Youtube ID', $obj->tableName . '.youtubeid'));
array_push($arrSearchColumn, array('Deskripsi', $obj->tableName . '.shortdesc')); 

$arrColumn = array ();
array_push($arrColumn, array('Kode','code',120));
array_push($arrColumn, array('Judul','title'));
array_push($arrColumn, array('Youtube ID','youtubeid',250));
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