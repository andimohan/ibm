<?php
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 

$sql = 'select * from ap where  statuskey in (1,2) and reftabletype = 318 and currencykey = 2';
$rs = $class->oDbCon->doQuery($sql);

foreach($rs as $apRow){
    $refkey = $apRow['refheaderkey'];
    $rsCommission = $emklCommission->getDataRowById($refkey);
    
    $usdAmount = ($rsCommission[0]['rate'] > 0 ) ? $rsCommission[0]['grandtotal'] / $rsCommission[0]['rate'] : $rsCommission[0]['grandtotal'] ;
    $sql = 'update ap set amount = '.$usdAmount.', outstanding = '.$usdAmount.', rate = '.$rsCommission[0]['rate'].' where pkey = ' .$apRow['pkey'] ;
    
    echo $sql.'<Br>';
    $class->oDbCon->execute($sql);
}

$class->oDbCon->endTrans();
    
echo 'done';
 
?>