<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 
 
$rsSalesOrder =  $truckingServiceOrder->searchData($truckingServiceOrder->tableName.'.statuskey',6,true);
foreach($rsSalesOrder as $row)
    $truckingServiceOrder->updateTotalSharedProfit($row['pkey']);
    
$class->oDbCon->endTrans();
    
echo 'done';
 
?>