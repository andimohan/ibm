<?php
 
include_once '../../_config.php';  
include_once '../../_include-v2.php';
   
includeClass(array('Marketplace.class.php','SalesOrder.class.php'));

$shopee = new Shopee();
$salesOrder = new SalesOrder();

if(!empty($_GET['orderid'])){ 
    $orderId = $_GET['orderid'];
}else { 
    die;
}

$payload = $shopee->createJsonBody(array('ordersn_list' => array($orderId))); 
$response = $shopee->executeRequest('orders/detail', $payload); 
$response = json_decode($response,true); 

echo '<pre>';
print_r($response);
echo '</pre>';

die;

?>