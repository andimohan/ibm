<?php
 
include_once '../../_config.php';  
include_once '../../_include-v2.php';
   
includeClass(array('Marketplace.class.php','SalesOrder.class.php'));

$tokopedia = new Tokopedia();
$salesOrder = new SalesOrder();

if(!empty($_GET['orderid'])){ 
    $orderId = $_GET['orderid'];
}elseif(!empty($_GET['invoice'])){ 
    $rs = $salesOrder->searchDataRow(array('marketplaceorderid'),' and '.$salesOrder->tableName.'.refcode = ' . $class->oDbCon->paramString($_GET['invoice']));
    $orderId = $rs[0]['marketplaceorderid'];
}else { 
    die;
}

$url = $tokopedia->url . 'v2/fs/'.$tokopedia->fsid.'/order?order_id='.$orderId; 
$response = $tokopedia->execute($url); 
echo '<pre>';
print_r($response);
echo '</pre>';

die;

?>