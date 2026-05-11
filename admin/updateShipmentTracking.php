<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php'; 

includeClass("SalesOrder.class.php");
 
$salesOrder = new SalesOrder();

$value = (isset($_POST) && !empty($_POST['value'])) ? $_POST['value'] : '';
$id = (isset($_POST) && !empty($_POST['id'])) ? $_POST['id'] : '';
	  	  
$errCode = $salesOrder->updateShipmentTracking($id,$value);
  
echo json_encode($errCode); 
die;
  
?>