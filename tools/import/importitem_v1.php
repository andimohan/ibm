<?php

require_once '../../_config.php'; 
require_once '../../_include.php';  
require_once '../../assets/vendor/autoload.php';  
require_once 'function.php';   

$obj = new Item();  
validateSecurity($obj, 'item', $spreadsheet);
 
$arrTable = array( 
            'item', 
            'item_category',
            'service_category',
            'item_unit' ,
            'item_unit_conversion',
            'item_in_header',
            'item_in_detail',
            'item_out_header',
            'item_out_detail',
            'item_movement',
            'item_in_warehouse',
            'purchase_order_header',
            'purchase_order_detail',
            'sales_order_header',
            'sales_order_detail',
            'item_adjustment_header',
            'item_adjustment_detail',
);  
 
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>  
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
<title>Upload - Item</title>  
</head> 
<body>    
    
<div style="padding: 1em"> 
    <div class="import-template">
    <h1>Updating Item...</h1>
        <ul class="progress-list"> 
            <?php
                    
                define('BARANG','barang');
                define('JASA','jasa');
               
                if (isset($_POST) && !empty($_POST['chkReset'])){ 
                    resetTable($class,$arrTable);
                    $class->oDbCon->execute('update item set cogs = 0');
                }
                
                $warehousekey = 1 ;    
                $itemAdjCtr = 0 ;
                $arr['hidDetailKey'] = array();
                
                $rsWeightUnit = $itemUnit->searchData();
                foreach($rsWeightUnit as &$unit)
                    $unit['name'] = strtolower($unit['name']);
                unset($unit);
                $rsWeightUnit = array_column($rsWeightUnit,'pkey','name');
            
                
                for ($row = 2; $row <= $highestRow; ++$row) {
                    $obj = new Item(); 
                    $objService = new Service();
                    $objCategory = new ItemCategory();
                    $objCategoryService = new  ServiceCategory();
                    $objBrand = new Brand();
                    $objDivision = new Division();
                    $objUnit = new ItemUnit();

 		            $code = $worksheet->getCellByColumnAndRow(1, $row)->getValue();
 		            $divisionName = $worksheet->getCellByColumnAndRow(2, $row)->getValue();
                    $name = $worksheet->getCellByColumnAndRow(3, $row)->getValue();
                    $name = $obj->removeMultipleAndUnusedSpace($name);
                    $tipe = $worksheet->getCellByColumnAndRow(4, $row)->getValue();
                    $categoryList = $worksheet->getCellByColumnAndRow(5, $row)->getValue();
                    $brandName = $worksheet->getCellByColumnAndRow(6, $row)->getValue();
                    $gramasi = $worksheet->getCellByColumnAndRow(7, $row)->getValue();
                    $weightUnit = $worksheet->getCellByColumnAndRow(8, $row)->getValue();
                    $weightUnitKey =  $rsWeightUnit[strtolower($weightUnit)];
                    $qty = $worksheet->getCellByColumnAndRow(9, $row)->getValue();
                    $unitName = $worksheet->getCellByColumnAndRow(10, $row)->getValue();
                    $minQty = $worksheet->getCellByColumnAndRow(11, $row)->getValue();
                    $maxQty = $worksheet->getCellByColumnAndRow(12, $row)->getValue();
                    $COGS = $worksheet->getCellByColumnAndRow(13, $row)->getValue();
                    $sellingPrice = $worksheet->getCellByColumnAndRow(14, $row)->getValue();
                    $shortDescription = $worksheet->getCellByColumnAndRow(15, $row)->getValue();
                    
                      
                    $objItem = $obj;
                    $objItemCategory = $objCategory;
                        
                    if (strtolower($tipe) == JASA){ 
                        $objItem = $objService;
                        $objItemCategory = $objCategoryService; 
                    }
                    
   		            // cek kategori sudah ada atau blm ...
                    // category gk mungkin di edit, karena jika beda nama / beda paent pasti ud dianggap kategori baru
                    $categoryList = explode("/", $categoryList);
                    $categoryKey = isExistCategoryPath($categoryList, $objItemCategory); 
                    
                    if( $categoryKey == 0 ){
                      //kalau belum ada, maka masukkan kategori tersebut
                      
                      $parentKey = 0; 
                      foreach($categoryList as $categoryName) { 
                        $categoryName = trim($categoryName);
                        
                        $criteria = ' AND '.$objItemCategory->tableName.'.name = '.$obj->oDbCon->paramString($categoryName).' AND '.$objItemCategory->tableName.'.parentkey = '.$obj->oDbCon->paramString($parentKey) ;
                        $rsCategory = $objItemCategory->searchData('', '', true, $criteria);
                           
                        if( empty($rsCategory)) {
                          $benchmark = array('field' => 'name' , 'value' => $categoryName);   
                          $arrParam = array();
                          $arrParam['code'] = 'xxxx';
                          $arrParam['name'] = $categoryName;
                          $arrParam['isLeaf'] = 1;
                          $arrParam['selCategory'] = $parentKey;
                          $arrParam['selStatus'] = 1; 
                          
                          $result = addData($objItemCategory,$benchmark, $arrParam, ' and '.$objItemCategory->tableName.'.parentkey = '.$obj->oDbCon->paramString($parentKey));
                             
                          $parentKey = $result['pkey'];
                        }
                        else{
                          $parentKey = $rsCategory[0]['pkey'];
                        }  
                      } //loop "cat1 / cat2 / ..."
                      
                      $categoryKey = $parentKey;
                    }
                    
                        
                    // cek divisi sudah ada atau blm ...  
                    $divisionkey = 0;
                    if(!empty($divisionName)){ 
                        $rsDivision = $objDivision->searchData($objDivision->tableName.'.name', $divisionName);
                        if (empty($rsDivision)){
                            $benchmark = array('field' => 'name' , 'value' => $divisionName);   
                            $arrParam = array(); 
                            $arrParam['selStatus'] = 1; 
                            $arrParam['code'] = 'xxxx';
                            $arrParam['name'] = $divisionName; 
                            $result = addData($objDivision,$benchmark, $arrParam);  
                            $divisionkey = $result['pkey'];
                        }else{
                            $divisionkey = $rsDivision[0]['pkey'];
                        }        
                    }
                    
                                     
                    
                    // cek merk sudah ada atau blm ...  
                    $brandkey = 0;
                    if(!empty($brandName)){ 
                        $rsBrand = $objBrand->searchData($objBrand->tableName.'.name', $brandName);
                        if (empty($rsBrand)){
                            $benchmark = array('field' => 'name' , 'value' => $brandName);   
                            $arrParam = array(); 
                            $arrParam['selStatus'] = 1; 
                            $arrParam['code'] = 'xxxx';
                            $arrParam['name'] = $brandName; 
                            $result = addData($objBrand,$benchmark, $arrParam);  
                            $brandkey = $result['pkey'];
                        }else{
                            $brandkey = $rsBrand[0]['pkey'];
                        }        
                    }
                    
                    
                    // cek unit sudah ada atau blm ...  
                    if (!empty($unitName))
                        $rsUnit = $objUnit->searchData($objUnit->tableName.'.name', $unitName);
                    else
                        $rsUnit = $objUnit->getDefaultData();
                        
                    if (empty($rsUnit)){
                        $benchmark = array('field' => 'name' , 'value' => $unitName);   
                        $arrParam = array(); 
                        $arrParam['selStatus'] = 1; 
                        $arrParam['code'] = 'xxxx';
                        $arrParam['name'] = $unitName; 
                        $result = addData($objUnit,$benchmark, $arrParam);  
                        $unitkey = $result['pkey'];
                    }else{
                        $unitkey = $rsUnit[0]['pkey'];
                    }        
                    

                    //$benchmark = (!empty($code)) ? array('field' => 'code' , 'value' => $code) :  array('field' => 'policenumber' , 'value' => $registrationNumber);  
                    if (!empty($code)){
                        $benchmark = array('field' => 'code' , 'value' => $code);
                        $overwriteCode = true;
                    }else{
                        $benchmark = array('field' => 'name' , 'value' => $name);
                        $overwriteCode = false;
                    }
                     
                    
                    
                    $arrParam = array(); 
                    $arrParam['selStatus'] = 1;
                    $arrParam['overwriteCode'] = $overwriteCode;
                    $arrParam['code'] = (!isset($code)) ? '' : $code; // menghindari null
                    $arrParam['name'] = $name;
                    $arrParam['hidCategoryKey'] = $categoryKey;
                    $arrParam['selDivisionKey'] = $divisionkey;
                    $arrParam['sellingPrice'] = $sellingPrice;
                    $arrParam['selBaseUnitKey'] = $unitkey;
                    $arrParam['selDefaultTransUnitKey'] = $unitkey;
                    $arrParam['minStockQty'] = 0;
                    $arrParam['maxStockQty'] = 0;
                    $arrParam['gramasi'] = $gramasi; 
                    $arrParam['shortdescription'] = $shortDescription; 
                    $arrParam['selWeightUnit'] = $weightUnitKey;
                    $arrParam['hidBrandKey'] = $brandkey;  
                    $arrParam['minStockQty'] =  (!isset($minQty) || $minQty > $maxQty ) ? 0 : $minQty; 
                    $arrParam['maxStockQty'] =  (!isset($maxQty)) ? 0 : $maxQty;  
                    
                    if(empty($arrParam['code']) && empty($arrParam['name'])) continue;
                    
                    $result = addData($objItem,$benchmark, $arrParam);   
                     
  		            // data for item in
                    if(isset($result['pkey']) && $qty > 0){  
                      $rsItem = $objItem->getDataRowById($result['pkey']);
                      if (!empty($rsItem)){ 
                          $qtyBefore = $itemMovement->getItemQOH($rsItem[0]['pkey'],$warehousekey);
                          $itemValue = (!empty($COGS)) ? $COGS : $rsItem[0]['cogs'];

                          $arr['hidDetailKey'][$itemAdjCtr] = 0;  
                          $arr['hidItemKey'][$itemAdjCtr] = $rsItem[0]['pkey']; 
                          $arr['qtyBefore'][$itemAdjCtr] = $qtyBefore; 
                          $arr['qtyAfter'][$itemAdjCtr] = $qty; 
                          $arr['qtyAdjust'][$itemAdjCtr] = $qty - $qtyBefore; 
                          $arr['baseUnitKey'][$itemAdjCtr] = 1;   
                          $arr['unitConvMultiplier'][$itemAdjCtr] = 1; 
                          $arr['COGS'][$itemAdjCtr] = $itemValue; 

                          $itemAdjCtr++;
                      } 
                    }
                       
                    
                }    
             
                  //proses ItemIn - begin
                  $obj->oDbCon->startTrans();
                  if (!empty($arr['hidDetailKey'])){ 
                      $trdate = date('d / m / Y');
 
                      $itemAdjustment = new ItemAdjustment();

                      $arr['pkey'] = $itemAdjustment->getNextKey($itemAdjustment->tableName);
                      $arr['code'] = 'xxxxx';
                      $arr['trDate'] = $trdate;
                      $arr['trDesc']  = 'Import';
                      $arr['selWarehouseKey'] = $warehousekey; 
                        
                      $itemAdjustment->addData($arr);
                  }

                $obj->oDbCon->endTrans();
                //proses ItemIn - end

                $obj->setLog("Import item done");
                echo '<li class="text-blue-munsell">Inserting data to <strong>'.$arrTable[0].'</strong>. done.</li>';  
  
            ?>
        </ul>
    </div>
</div>     
    
</body> 
</html> 
