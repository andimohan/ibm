<?php  
// ========================================================================== INITIALIZE ==========================================================================

include '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass('Car.class.php');
$car = createObjAndAddToCol(new Car()); 


$obj = $car;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'carForm';
$quickView = false;
  
$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nomor Polisi', $obj->tableName . '.policenumber')); 
array_push($arrSearchColumn, array('Seri', $obj->tableSeries . '.name')); 
array_push($arrSearchColumn, array('Merk', $obj->tableBrand . '.name')); 
array_push($arrSearchColumn, array('Kategori', $obj->tableCategory . '.name'));
array_push($arrSearchColumn, array('Supplier', $obj->tableSupplier . '.name'));
    
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = ''; 

function generateQuickView($obj,$id){ 
	    
	$detail = '';
	   
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
