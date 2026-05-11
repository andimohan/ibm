<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $preorderItem;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'preorderItemForm';
 
$arrSearchColumn = array(
	'0' => array('Kode', $obj->tableName . '.code'), 
	'1' => array('Nama', $obj->tableItem . '.name')  
); 		 
		
$arrColumn = array (
  '0' => array('Kode','code',70,'true','left'),
  '1' => array('Nama','itemname',450,'true','left'), 
  '2' => array('Tgl. Tutup','closingdate',100,'true','center','date'),  
  '3' => array('Harga PO','poprice',100,'true','right','integer'),  
  '4' => array('Status','statusname','','true','left'),
);   
  
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){  
  	$detail = '';
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>