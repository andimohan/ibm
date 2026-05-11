<?php  

require_once '../_config.php';
require_once '../_include-v2.php';

includeClass(array('SalesOrder.class.php')); 

if(!isset($_GET) || empty($_GET['id'])) die;

$salesOrder = new SalesOrder();
$salesOrder->sendAttachedInvoice($_GET['id']);

echo 'done'; 
die;
 
        
?>