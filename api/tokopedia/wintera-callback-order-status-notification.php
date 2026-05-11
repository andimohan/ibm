<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

$ACTION = $_SERVER['REQUEST_METHOD'];  
 
$fileGet = file_get_contents("php://input"); 
$_RESPONSE = json_decode($fileGet,true);

if(empty($_RESPONSE)) die;
    
$shopId = $_RESPONSE['shop_id'];
$orderId = $_RESPONSE['order_id'];
$orderStatus = $_RESPONSE['order_status'];

// cuma proses sesuai yg kita mau saja dulu statusnya
if(!in_array($orderStatus, array(0,10,220,500))) {
http_response_code(200);
die;
}


// NANTI PERLU DIUPDATE agar bisa pisah per domain
require_once  '../../_mp-client.php';  
if(!isset(MP_TP_CLIENT[$shopId])) die; 

// manipulasi domain name, agar diproses sesuai dengan domain maing2
$_SERVER['HTTP_HOST'] = MP_TP_CLIENT[$shopId];

//setLog($_RESPONSE);

require_once '_global.php';  
includeClass(array('SalesOrder.class.php'));

switch($orderStatus){
    case 0:
    case 10:   $tokopedia->cancelSalesOrderById($orderId);
               break;
    case 220 : includeClass(array('Customer.class.php')); 
               $tokopedia->addSalesOrderById($orderId);
               break;
    case 500 :  $tokopedia->updateDeliveredOrders($orderId);
                break;
}
 
http_response_code(200);
die;

function setLog($msg){   
    if(is_array($msg)) $msg = print_r($msg, true);
    error_log ($msg.chr(13),3,'../../log/manual-log-tp');
}



/*
0 	Seller cancel order.
2 	Order Reject Replaced.
3 	Order Reject Due Empty Stock.
4 	Order Reject Approval.
5 	Order Canceled by Fraud
10 	Order rejected by seller.
11 	Order Pending Replacement.
100 	Pending order.
103 	Wait for payment confirmation from third party.
200 	Payment confirmation.
220 	Payment verified, order ready to process.
221 	Waiting for partner approval.
400 	Seller accept order.
450 	Waiting for pickup.
500 	Order shipment.
501 	Status changed to waiting resi have no input.
520 	Invalid shipment reference number (AWB).
530 	Requested by user to correct invalid entry of shipment reference number.
540 	Delivered to Pickup Point.
550 	Return to Seller.
600 	Order delivered.
601 	Buyer open a case to finish an order.
690 	Fraud Review
691 	Suspected Fraud
695 	Post Fraud Review
698 	Finish Fraud Review
699 	Order invalid or shipping more than 25 days and payment more than 5 days.
700 	Order finished.
701 	Order assumed as finished but the product not arrived yet to the buyer.*/
    
?>