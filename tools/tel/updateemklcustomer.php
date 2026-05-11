<?php 
die;

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 
 
$class->oDbCon->startTrans();

$emklJobOrder = new EMKLJobOrder();
$emklJobOrderHeader = new EMKLJobOrderHeader();
$rsJOType = $emklJobOrder->getTableKeyAndObj($emklJobOrder->tableName,array('key'));

$emklPurchaseOrder = new EMKLPurchaseOrder();
$rsPO = $emklPurchaseOrder->searchData();

foreach($rsPO as $poRow){
    
    if($poRow['reftabletype'] == $rsJOType['key']){ 
         $rsJO = $emklJobOrder->getDataRowById($poRow['refkey']); 
        if(empty($rsJO[0]['customerkey'])) continue;
         $sql = 'update '.$emklPurchaseOrder->tableName.' set customerkey = '. $rsJO[0]['customerkey'].' where pkey = ' . $poRow['pkey'];
    }else{     
         $rsJO = $emklJobOrderHeader->getDataRowById($poRow['refjoheaderkey']); 
         if(empty($rsJO[0]['customerkey'])) continue;
         $sql = 'update '.$emklPurchaseOrder->tableName.' set customerkey = '. $rsJO[0]['customerkey'].' where pkey = ' . $poRow['pkey'];
 
    }
    
    echo $sql.'<br>';
    $class->oDbCon->execute($sql); 

}

/*
$sql = 'select * from emkl_job_order_header';
$rs = $class->oDbCon->doQuery($sql);
 
foreach ($rs as $emklRow){
     
    $rsSalesDetail = $emklJobOrder->getDetailWithRelatedInformation($emklRow['pkey']);
    
    $invoiceTo = 0;
    foreach($rsSalesDetail as $detailRow){
           if (empty($invoiceTo) || $detailRow['customerkey'] == $emklRow['customerkey'])
                $invoiceTo = $detailRow['customerkey'];
            
    }
    
    $sql = 'update emkl_job_order_header set invoicetokey = '.$invoiceTo.' where pkey = ' .$emklRow['pkey'];
    $class->oDbCon->execute($sql);
}
*/

$class->oDbCon->endTrans();
echo '<bR><br>done ';
?>