<?php
require_once '../_config.php'; 
require_once '../_include.php';  
 
$shopee->createProduct(1829);

/*
$totalItems = 0;

$rsItem = $item->searchData('','',true,'','order by code asc');
for($i=0;$i<count($rsItem);$i++){  
    $totalItems++;
    $shopee->updateProductsQOH($rsItem[$i]['pkey']);
}
 
echo $rsItem[$i-1]['code']; 
echo '<br>';
*/

/*

$arrSKU = array();
$offset = 0;
$totalItem = 0;
$totalItemLink = 0;
$totalUniqueItem = 0;
$flag = false;
do{
 
    $payload = $shopee->createJsonBody(array(
                'pagination_offset' => intval($offset),
                'pagination_entries_per_page' => 100, 
            )); 

    $response = $shopee->executeRequest('items/get', $payload);
 
    $response = json_decode($response,true);
    $rsItem = $item->searchData();
    $rsItem = array_column($rsItem,null,'code');
    
    $arrItems = $response['items'];
    
    $arrException = array('1473017455', '1607904581', '1410168503','1403372358', '1410168539');
    foreach($arrItems as $itemRow){
        if ( in_array($itemRow['item_id'],$arrException )) continue;
        
        $totalItem++;
        $itemCode = $itemRow['item_sku'];
        
        if(!in_array($itemCode, $arrSKU)){ 
            array_push($arrSKU, $itemCode); 
            $totalUniqueItem++;
            if (!isset($rsItem[$itemCode])){ 
                echo $itemRow['item_id'] . ' tidak ad di sistem'.'<br>';
            }else{ 
                $marketplace->addItemMarketplaceLink( $rsItem[$itemCode]['pkey'], $itemRow['item_id']);
                $totalItemLink++;
            }
        } else { 
            echo $itemRow['item_sku'].'<br>';
        }

    }

    $offset += 100;
    
    if($response['more']) $flag = true;
    if ($offset >= 2000) $flag = false;
        
}while($flag);
  
echo 'total item shopee ' . $totalItem.'<bR>';
echo 'total item unik shopee ' . $totalUniqueItem.'<bR>';
echo 'total item link ' . $totalItemLink.'<bR>';
*/

echo 'done ' ;

?>