<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass(array('AP.class.php','APEmployee.class.php'));
$apEmployee = createObjAndAddToCol(new APEmployee());

$obj = $apEmployee;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
   
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'apEmployeeForm';
$quickView = false;
  

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Tgl. Transaksi', $obj->tableName . '.trdate')); 
array_push($arrSearchColumn, array('Referensi', $obj->tableName. '.refcode'));
array_push($arrSearchColumn, array('Karyawan', $obj->tableEmployee. '.name'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer. '.name'));
array_push($arrSearchColumn, array('Jumlah', $obj->tableName. '.amount'));
array_push($arrSearchColumn, array('Catatan', $obj->tableName. '.trdesc'));
array_push($arrSearchColumn, array('Referensi', $obj->tableCashBankRealization. '.refcode'));
array_push($arrSearchColumn, array('Referensi', $obj->tableCashBankRealization. '.refcode2'));
array_push($arrSearchColumn, array('Referensi', $obj->tableCashBankRealization. '.refcode3'));
 
		  
$overwriteContextMenu['showDetail'] = '';
$overwriteContextMenu['hideDetail'] = '';  
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>