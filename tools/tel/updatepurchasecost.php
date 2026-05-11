<?php

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$sql = 'select * from emkl_purchase_order_header where 
        taxvalue <> 0 and 
        refkey <> 0 and 
        statuskey <> 4
        ' ;

$rs = $class->oDbCon->doQuery($sql);

// sekalian cek ad gk order yg detail IDR, tp kena tax
/*$arrkey = array_column($rs,'pkey');
$sql = 'select distinct(emkl_purchase_order_header.code), currencykey from emkl_purchase_order_detail,emkl_purchase_order_header
            where 
            emkl_purchase_order_header.pkey =  emkl_purchase_order_detail.refkey and
            emkl_purchase_order_header.currencykey <> 1 and
            emkl_purchase_order_header.taxvalue <> 0
        ';
echo $sql.'<br>';
$rsTest = $class->oDbCon->doQuery($sql); 
$arrkey = array_column($rsTest,'code');
echo implode('<br>',$arrkey);
die;*/

foreach($rs as $row) { 
    $rate = $row['rate'];
    $jokey = $row['refkey'];
    
    $emklJobOrderExport->oDbCon->startTrans();
    $emklJobOrderExport->updateTotalBuying($jokey); 
    $emklJobOrderExport->oDbCon->endTrans();
}

echo 'done';
 
?>