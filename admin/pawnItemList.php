<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $pawnItem;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'pawnItemForm';
 
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama', $obj->tableName . '.name'));
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory. '.name') );
 
$arrColumn = array ();
array_push($arrColumn, array('Kode','code',100));
array_push($arrColumn, array('Nama','name')); 
array_push($arrColumn, array('Kategori','categoryname',150));
array_push($arrColumn, array('Harga Baru','cogs',100,'right','integer'));
array_push($arrColumn, array('Harga Second','secondprice',100,'right','integer'));
array_push($arrColumn, array('Nilai Gadai','sellingprice',100,'right','integer'));
array_push($arrColumn, array('Jumlah','qtyonhand',70 ,'right','integer'));
array_push($arrColumn, array('Satuan','baseunitname',70));
array_push($arrColumn, array('Status','statusname',70));
		 

function generateQuickView($obj,$id){  
	$detail = '';  
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>