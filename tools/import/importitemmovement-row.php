<?php

require_once '../../_config.php'; 
require_once '../../_include.php';  
require_once '../../assets/vendor/autoload.php';  
require_once 'function.php';   

// ITEM MOVEMENT BOLEH RESET, TP TIDAK KEHAPUS UTK TRANSAKSI PEMBELIAN / PENJUALAN
$arrTable = array(            
'item_in_header',
'item_in_detail',
'item_out_header',
'item_out_detail',
'item_movement',
'item_in_warehouse', 
'item_adjustment_header',
'item_adjustment_detail',
);  
 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<title>Upload - Item Movement</title>  
</head> 
<body>    
    
<div style="padding: 1em"> 
    <div class="import-template">
    <h1>Updating Item...</h1>
        <ul class="progress-list"> 
            <?php
                    
                define('BARANG','barang');
                define('JASA','jasa');
            
                define('MOVEMENT',array('in' => 1, 'out' => 2)); 
            
                if (!isset($obj)) $obj = new Item();
                if (isset($_POST) && !empty($_POST['chkReset'])) resetTable($obj,$arrTable);
            
                $objItem = new Item(); 
                $objItemIn = new ItemIn(); 
                $objItemOut = new ItemOut(); 
                $objWarehouse = new Warehouse();
                
                $warehousekey = 1 ;    
                $itemAdjCtr = 0 ;
                $arr['hidDetailKey'] = array();
            
                //search semua item. ubah kode jd index.
                $rsItemColl = $objItem->searchData('','',true,' and  '.$objItem->tableName.'.statuskey = 1 and  '.$objItem->tableName.'.itemtype = 1 '); 
                $arrItemCollByCode =  array_column($rsItemColl,null,'code');       
                $arrItemCollByName =  array_column($rsItemColl,null,'name');       
                
                $arrParam = array();
                $arrColl = array();
            
                $existItem = array();
            
                for ($row = 2; $row <= $highestRow; ++$row) {

                    $warehouseName  = $worksheet->getCellByColumnAndRow(1, $row)->getValue(); 
                    $movementDate = $worksheet->getCellByColumnAndRow(2, $row)->getValue(); 
                    
                    if (empty($movementDate)) continue;
                    
                    $movementDate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($movementDate);
                    $movementDate = $movementDate->format('d / m / Y'); 
                    
                    $itemcode = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $itemname = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $itemname = $obj->removeMultipleAndUnusedSpace($itemname);
                    $movementQty = $worksheet->getCellByColumnAndRow(5, $row)->getValue();    
                    $movementValue = $worksheet->getCellByColumnAndRow(6, $row)->getValue();      
                     
                    if ($movementQty == 0) continue;
                    
                    $arrItemColl = (!empty($itemcode)) ? $arrItemCollByCode : $arrItemCollByName;
                    $itemColIndex = (!empty($itemcode)) ? $itemcode : $itemname;
                         
                    if(empty($arrItemColl[$itemColIndex]['pkey'])){ 
                        echo $itemColIndex . ' not found.<br>';
                        continue;
                    }
                    
                    // mulai tampung data  
                    $movementType = ($movementQty < 0 ) ?  MOVEMENT['out'] : MOVEMENT['in']; 
                     
                    if (!empty($warehouse)){
                        $rsWarehouse = $objWarehouse->searchData($objWarehouse->tableName.'.name', $warehouseName);
                        $warehousekey = $rsWarehouse[0]['pkey'];
                    } else{ 
                        $warehousekey = $objWarehouse->getDefaultData();
                    }
                     
                    $arrIndex = $movementType.'-'.$movementDate;
                    
                    if (!isset($arrColl[$arrIndex])){
                        //set headers
                        $arrColl[$arrIndex]['movementType'] = $movementType;
                        $arrColl[$arrIndex]['code'] = 'xxxx';
                        $arrColl[$arrIndex]['trDate'] = $movementDate;
                        $arrColl[$arrIndex]['selWarehouseKey'] = $warehousekey;
                        $arrColl[$arrIndex]['trDesc'] = 'Auto Import';
                    } 
                     
                    if(!isset($existItem[$arrIndex])) 
                        $existItem[$arrIndex] = array();
                    
                    $itemkey = $arrItemColl[$itemColIndex]['pkey'];
                    $baseunitkey = $arrItemColl[$itemColIndex]['baseunitkey'];
                    
                    if (in_array($itemkey, $existItem[$arrIndex])){ 
                        // kalo itemnya sdh pernah diinput
                        for($i=0;$i<count($arrColl[$arrIndex]['hidItemKey']);$i++){
                            if ($arrColl[$arrIndex]['hidItemKey'][$i] == $itemkey){
                                $arrColl[$arrIndex]['qty'][$i] +=  abs($movementQty);
                                break;
                            } 
                        }
                    } else{

                        // set detail 
                        $itemCtr = (!isset($arrColl[$arrIndex]['hidDetailKey'])) ? 0 :  count($arrColl[$arrIndex]['hidDetailKey']);
                        $arrColl[$arrIndex]['hidDetailKey'][$itemCtr] = 0;  
                        $arrColl[$arrIndex]['hidItemKey'][$itemCtr] = $itemkey; 
                        $arrColl[$arrIndex]['selUnit'][$itemCtr] = $baseunitkey; 
                        $arrColl[$arrIndex]['qty'][$itemCtr] = abs($movementQty); 
                        $arrColl[$arrIndex]['COGS'][$itemCtr] = abs($movementValue); 
                            
                        array_push($existItem[$arrIndex], $itemkey);
                    }
                }    
            
                $indexKey = array_keys($arrColl); // ini harus unique 
            
                foreach( $indexKey as $index){

                    $arrTemp = $arrColl[$index];
                    
                    $obj = ($arrTemp['movementType'] == MOVEMENT['in'] ) ? new ItemIn() : new ItemOut();
                    
                 /*   echo '<br>INDEX : ' . $index .'<br>';
                    echo '<br> ' . $obj->tableName .'<br>';
                    print_r($arrTemp);
                    echo '<br><br>';*/
                    $result = $obj->addData($arrTemp);
                    $obj->changeStatus($result[0]['data']['pkey'], 2);
                   // print_r($result);
                        
                }
    
                echo 'done';
            ?>
        </ul>
    </div>
</div>     
    
</body> 
</html> 
