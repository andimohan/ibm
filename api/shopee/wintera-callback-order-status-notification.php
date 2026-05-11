<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

$ACTION = $_SERVER['REQUEST_METHOD'];    
$_RESPONSE = json_decode(file_get_contents("php://input"),true);  

if(empty($_RESPONSE)) die;
 
$code = $_RESPONSE['code'];
if ($code <> 3) die;

$shopId = $_RESPONSE['shop_id'];
$orderId = $_RESPONSE['data']['ordersn'];
$orderStatus = strtolower($_RESPONSE['data']['status']); 
 
// cuma proses sesuai yg kita mau saja dulu statusnya
if(!in_array($orderStatus, array('shipped','ready_to_ship','canceled','cancelled'))) {
http_response_code(200);
die;
}

// NANTI PERLU DIUPDATE agar bisa pisah per domain
require_once  '../../_mp-client.php'; 
if(!isset(MP_SH_CLIENT[$shopId])) die;

//setLog($_RESPONSE);

// manipulasi domain name, agar diproses sesuai dengan domain maing2
$_SERVER['HTTP_HOST'] = MP_SH_CLIENT[$shopId];

require_once '_global.php';  
includeClass(array('SalesOrder.class.php'));

switch($orderStatus){
    case 'canceled' : 
    case 'cancelled' :   $shopee->cancelSalesOrderById($orderId);
                    break; 
    case 'ready_to_ship' : includeClass(array('Customer.class.php')); 
               $shopee->addSalesOrderById($orderId);
               break; 
    case 'shipped' :  $shopee->updateDeliveredOrders($orderId,false);
                break;
}
 
http_response_code(200);
die;

function setLog($msg){  
    
    if(is_array($msg)) $msg = print_r($msg, true);
    error_log ($msg.chr(13),3,'../../log/manual-log-sh');
}


?>