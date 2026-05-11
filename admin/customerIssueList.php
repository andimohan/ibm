<?php  
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass("CustomerIssue.class.php");
$customerIssue = new CustomerIssue();

$obj = $customerIssue;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
 
if(!$security->isAdminLogin($securityObject,10,true));
 
$addDataFile = 'customerIssueForm';
 

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode', $obj->tableName . '.code'));
array_push($arrSearchColumn, array('Kode SO', $obj->tableSalesOrder . '.code'));
array_push($arrSearchColumn, array('Pelanggan', $obj->tableCustomer . '.name'));
array_push($arrSearchColumn, array('Masalah', $obj->tableName . '.issue'));  
 
$arrColumn = array ();
array_push($arrColumn, array('Kode','code',120));
array_push($arrColumn, array('Kode SO','salesordercode',120));
array_push($arrColumn, array('Pelanggan','customername'));
// array_push($arrColumn, array('Pelanggan','createdon',130,'center','date'));
// array_push($arrColumn, array('Telepon','phone',120));
array_push($arrColumn, array('Masalah','issue',200));
array_push($arrColumn, array('Status','statusname',70));
            
function generateQuickView($obj,$id){ 
 
	// $rs = $obj->getDataRowById($id);   
	
	// $description  = '<div class="data-card no-border">
	// 				<h1>Isi Pesan</h1>
	// 				<div style="width:100%">'.str_replace(chr(13),'<br>',$rs[0]['message']).'</div> 
	// 			</div>';
				
	// $detail = $description; 
  
 	// $detail .= '<div style="clear:both;"></div>';	 
    $detail = '';
  
	return $detail;  
}
 
// ========================================================================== STARTING POINT ==========================================================================
include ('dataList.php');
?>
