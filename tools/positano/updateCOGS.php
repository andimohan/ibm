<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php'; 

$class->oDbCon->startTrans(); 

$startDate = '01 / 07 / 2020';
$endDate = date('d / m / Y');

$rsBrand = $class->convertForCombobox($brand->searchData('','',true, ' and ('.$brand->tableName.'.statuskey = 1 )'),'pkey','name'); 
$rsItemCategory = $class->convertForCombobox($itemCategory->searchData('','',true, ' and ('.$itemCategory->tableName.'.statuskey = 1 )',' order by name asc'),'pkey','name'); 
$rsWarehouse = $class->convertForCombobox($warehouse->searchData('','',true, ' and ('.$warehouse->tableName.'.statuskey = 1 )'),'pkey','name'); 

echo '<form method="POST">';
echo $class->inputSelect('selBrand', $rsBrand);
echo $class->inputSelect('selItemCategory', $rsItemCategory);
echo $class->inputSelect('selWarehouse', $rsWarehouse);
echo $class->inputNumber('inputCOGS');
echo $class->inputHidden('action',array('value' => 'add'));
echo $class->inputSubmit('btnSubmit',$class->lang['submit']);
echo '</form>';



if (isset($_POST) && !empty($_POST['action'])){
    echo 'start...<br>' ;
    $brandkey = $_POST['selBrand'];
    $categorykey = $_POST['selItemCategory'];
    $warehousekey = $_POST['selWarehouse'];
    $inputCOGS =  $item->unFormatNumber($_POST['inputCOGS']);
    
    $rsItem = $item->searchData($item->tableName.'.statuskey','1',true, ' and warehousekey = '.$class->oDbCon->paramString($warehousekey).' and brandkey = '.$class->oDbCon->paramString($brandkey).' and categorykey = ' . $class->oDbCon->paramString($categorykey));
    $arrItemUpdated = array_column($rsItem,'pkey');
    
    $itemOut = new ItemOut();
    $itemIn = new ItemIn();
     
    $arrParam = array();
    
    $arrParam['hidItemKey'] = array();
    $arrParam['selUnit'] = array();
    $arrParam['qty'] = array();
    $arrParam['COGS'] = array();
    
    for($i=0;$i<count($rsItem);$i++){
        if ($rsItem[$i]['qtyonhand'] == 0) continue;
        if ($rsItem[$i]['cogs'] == $inputCOGS) continue;
         
        array_push($arrParam['hidItemKey'], $rsItem[$i]['pkey']);
        array_push($arrParam['selUnit'], $rsItem[$i]['baseunitkey']);
        array_push($arrParam['qty'], $rsItem[$i]['qtyonhand']);
        array_push($arrParam['COGS'], $inputCOGS);
        
		//$arrParam['COGS'][$i] = $rsItem[$i]['cogs']; 
        
        echo $rsItem[$i]['name'].' ' .$rsItem[$i]['qtyonhand'] .'<br>'; 
        
    }
    
    $arrParam['code'] = 'xxxx';
    $arrParam['selWarehouseKey'] = $warehousekey;
    $arrParam['selStatus'] = 1;
     
    //$arrParam['hidCustomerKey'] = 1;
    $arrParam['trDesc'] = 'Adj. COGS';
    $arrParam['trDate'] = date('d / m / Y');
    
    if(!empty($arrParam['hidItemKey'])){ 
       $itemOut->addData($arrParam);
       $itemIn->addData($arrParam);
    }
    
    // udpate profit loss
    
    //if($_POST['chkUpdateProfitLoss'] == 1){
            
        $sql = 'select 
                    sales_order_header.* 
                from 
                    sales_order_header, sales_order_detail 
                where
                    sales_order_header.pkey = sales_order_detail.refkey and
                    sales_order_header.statuskey in (2,3) and
                    sales_order_header.trdate between '.$class->oDbCon->paramDate($startDate,' / ').' and '.$class->oDbCon->paramDate($endDate,' / ').' and
                    sales_order_detail.itemkey in ('.$class->oDbCon->paramString($arrItemUpdated,',').')                                                                                                                  
                ';
        
        $rsSalesOrder = $class->oDbCon->doQuery($sql);
    
        foreach($rsSalesOrder as $salesRow){

            echo $salesRow['code'].'<br>';

            $sql = 'select * from sales_order_detail where refkey = '.$class->oDbCon->paramString($salesRow['pkey']).' and  sales_order_detail.itemkey in ('.$class->oDbCon->paramString($arrItemUpdated,',').') ';
            $rsDetail =  $class->oDbCon->doQuery($sql);

            foreach($rsDetail as $detailRow){ 

                $sql = 'update 
                            sales_order_detail 
                        set 
                            costinbaseunit = ' . $class->oDbCon->paramString($inputCOGS) .', 
                            profit = priceinbaseunit - costinbaseunit 
                        where 
                            pkey = '. $class->oDbCon->paramString($detailRow['pkey']);
                $class->oDbCon->execute($sql); 
                
                echo $sql.'<br>';

            } 
            
             $sql = 'update 
                    sales_order_header
                set 
                    profit = (select sum(profit * qtyinbaseunit) from sales_order_detail where refkey = '.$salesRow['pkey'] .')
                where pkey = '. $class->oDbCon->paramString($salesRow['pkey']); 
        
            $class->oDbCon->execute($sql);
        
        }  
 
   // }
    
    echo '<br>'; 
      
}

echo 'done';

$class->oDbCon->endTrans();
 
?>