<?php

include '../../_config.php';  
include '../../_include.php';
 
$obj = $salesOrder;
$securityObject = $obj->securityObject; // the value of security object is manually inserted to handle 
										// some modules that have different security object from that of their class

 
if(!$security->isAdminLogin($securityObject,11,true));
 
	foreach ($_POST as $k => $v) {
		
		if (!is_array($v))
			 $v = trim($v);  
		
		$arr[$k] = $v;     
	}  
	 

	$arrReturn = array(); 
	$arr['createdBy'] = base64_decode($_SESSION[$obj->loginAdminSession]['id']);
	$arr['selStatus'] = 1;
	$arr['code'] = 'xxxxx';
	$arr['hidCustomerKey'] = 1;
	$arr['recipientEmail'] = '';
$arr['chkIsFullDeliver'] = 1;
	$arr['shipmentFee'] = 0;
	$arr['etcCost'] = 0;
	$arr['pointValue'] = 0; 
	$arr['trDate'] = date('d / m / Y'); 
	$arr['trNotes'] = '';
	$arr['selShipment'] = '';


    $rsPayment = $termOfPayment->searchData('systemVariable',1,true); 
	$arr['selTermOfPaymentKey'] = $rsPayment[0]['pkey']; 

    $rsWarehouse = $warehouse->searchData('systemVariable',1,true);
	$arr['selWarehouseKey'] = $rsWarehouse[0]['pkey']; 

	$arr['selSalesKey'] = base64_decode($_SESSION[$obj->loginAdminSession]['id']);

	$arrReturn = $obj->addData($arr);
 	 
	echo json_encode($arrReturn);  
	die; 

?> 
