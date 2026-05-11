<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('PurchaseRequest.class.php'));
$purchaseRequest = new PurchaseRequest();

$obj = $purchaseRequest;   

// khusus kalo nyari header aja

$fieldValue = $obj->tableName.'.code';
include 'ajax-general.php';
  
die;
  
?>