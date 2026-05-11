<?php

die('deprecated');
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

// tidak menghitung diskon dan pajak

$class->oDbCon->startTrans(); 

$startDate = '01 / 07 / 2020';
$endDate = date('d / m / Y');

$rsSalesOrder = $salesOrder->searchData('','',true,' and '.$salesOrder->tableName.'.statuskey in (2,3) and trdate between '.$class->oDbCon->paramDate($startDate,' / ').' and '.$class->oDbCon->paramDate($endDate,' / '));
 
foreach($rsSalesOrder as $salesRow){
    
    echo $salesRow['code'].'<br>';
    
    $rsDetail = $salesOrder->getDetailById($salesRow['pkey']);
    
    foreach($rsDetail as $detailRow){
        
        $itemkey = $detailRow['itemkey'];
        $rsItem = $item->getDataRowById($itemkey);
        $brandkey = $rsItem[0]['brandkey'];
        $itemcategorykey = $rsItem[0]['categorykey'];
        
        $cogs = $rsItem[0]['cogs'];
        
        // kalo cogs kosong, ambil dr nilai pemasukan terakhir
        // asumsi karena adjustment COGS terakhir yg sejenis
        if($cogs == 0){
            $sql = 'select 
                        item_in_detail.costinbaseunit
                    from 
                        item_in_detail,
                        item
                    where  
                        item_in_detail.itemkey = item.pkey and
                        item.brandkey = '.$brandkey.' and
                        item.categorykey =  '.$itemcategorykey.' 
                    order by item_in_detail.pkey desc limit 1'; 
          
            $rsItemDetail =  $class->oDbCon->doQuery($sql); 
            $cogs = $rsItemDetail[0]['costinbaseunit'];
        }
        
        $sql = 'update 
                    sales_order_detail 
                set 
                    costinbaseunit = ' . $class->oDbCon->paramString($cogs) .',
                    profit = priceinbaseunit - costinbaseunit 
                where pkey = '. $class->oDbCon->paramString($detailRow['pkey']); 
        
        $class->oDbCon->execute($sql);
        
        
         
    }
    
       $sql = 'update 
                    sales_order_header
                set 
                    profit = (select sum(profit * qtyinbaseunit) from sales_order_detail where refkey = '.$salesRow['pkey'] .')
                where pkey = '. $class->oDbCon->paramString($salesRow['pkey']); 
        
        $class->oDbCon->execute($sql);
        
        
}  

echo 'done';

$class->oDbCon->endTrans();
 
?>