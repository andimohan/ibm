<?php

die("die, comment open for reset transaction");

include_once '../_config.php';  
include_once '../_include-v2.php';

includeClass(array("SalesOrder.class.php"));

$salesOrder = new SalesOrder();

//hapus semua movement
$sql = array(
'delete from item_movement',
'delete from transaction_log',
'delete from item_in_warehouse',
'delete from general_journal_header',
'delete from general_journal_detail',
'update sales_order_header set statuskey = 1 where statuskey in (2,3)',
'update purchase_order_header set statuskey = 1 where statuskey in (2,3)',
'update item_in_header set statuskey = 1 where statuskey in (2,3)',
'update item_in_detail set qty = 9999999,  qtyinbaseunit = 9999999,receivedqtyinbaseunit = 9999999' 
);


$salesOrder->oDbCon->startTrans(); 
        
foreach($sql as $sqlRow){ 
$salesOrder->oDbCon->execute($sqlRow);   
} 


$salesOrder->oDbCon->endTrans();

echo 'done';
?>