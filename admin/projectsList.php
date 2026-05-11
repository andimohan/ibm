<?php  
require_once '../_config.php'; 
require_once '../_include.php'; 

$obj = $projects;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class
										
if(!$security->isAdminLogin($securityObject,10,true));

$addDataFile = 'projectsForm';
$quickView = true;

$arrSearchColumn = array ();
array_push($arrSearchColumn, array('Kode',$obj->tableName . '.code'));
array_push($arrSearchColumn, array('Nama',$obj->tableCustomer . '.name'));  
  
$arrColumn = array ();
array_push($arrColumn, array(ucwords($obj->lang['code']),'code',100)); 
array_push($arrColumn, array(ucwords($obj->lang['date']),'trdate',100,'center','date'));  
array_push($arrColumn, array(ucwords($obj->lang['description']),'trdesc')); 
array_push($arrColumn, array(ucwords($obj->lang['customer']),'customername',100)); ; 
array_push($arrColumn, array(ucwords($obj->lang['status']),'statusname',70));
	  

/*$printTransactionFunction = $class->generatePrintContextMenu('print','printTruckingCostCashOut');  
$overwriteContextMenu["printSeparator"] = "-";
$overwriteContextMenu["print"] = array("name" => $obj->lang['printTransaction'],"icon" =>"print","callbackFunction" => $printTransactionFunction);
*/ 
//$overwriteContextMenu['showDetail'] = '';
//$overwriteContextMenu['hideDetail'] = ''; 



function generateQuickView($obj,$id){ 
	 
	return '';  
}

 
include ('dataList.php');

?>
