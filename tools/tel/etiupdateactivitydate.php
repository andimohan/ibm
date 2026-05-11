<?php
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 

$sql = 'select * from trucking_service_order_header where year(trdate) = \'2021\' ' ;
$rs = $class->oDbCon->doQuery($sql);
 
foreach($rs as $row) 
    $truckingServiceOrder->updateWOActivityDate($row['pkey']);
 
$class->oDbCon->endTrans();
    
echo 'done';
 
?>