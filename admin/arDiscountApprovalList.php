<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('ARDiscountApproval.class.php'));
$arDiscountApproval = createObjAndAddToCol(new ARDiscountApproval());
/*$arPayment = createObjAndAddToCol(new ARPayment());
$customer = createObjAndAddToCol(new Customer());
$warehouse = createObjAndAddToCol( new Warehouse()); 
$currency = createObjAndAddToCol( new Currency());*/

$obj = $arDiscountApproval;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'arDiscountApprovalForm';
$quickView = false;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code')); 
array_push($arrSearchColumn, array('Kode Pembayaran Piutang', $obj->tableARPayment . '.code')); 
array_push($arrSearchColumn, array('Tanggal', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));
array_push($arrSearchColumn, array('Gudang', $obj->tableWarehouse. '.name')); 
 
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';

function generateQuickView($obj,$id){ 
   
	$detail ='';
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
