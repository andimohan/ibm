<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php';

includeClass('Carrier.class.php');
$carrier = createObjAndAddToCol(new Carrier()); 

$obj = $carrier;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'carrierForm';
$quickView = false;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode',$obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama',$obj->tableName . '.name')); 
  
$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100));
array_push($arrColumn, array(ucwords($obj->lang['name']),'name')); 
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
	  

$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){
	return ''; 
}

 
include ('dataList.php');

?>