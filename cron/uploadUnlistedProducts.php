<?php     

require_once '../_config.php'; 
require_once "../_include.php"; 

$obj = $lazada;
$hasQOHOnly = ' having qtyonhand > 0 ';

$rsLinkItem = $marketplace->searchLinkItem();
$arrItemLinkedWithMarketplace = array_column($rsLinkItem,'refkey');

$rsUnlistedItem = $item->searchData('','',true,' and '.$item->tableName.'.pkey not in ('.$obj->oDbCon->paramString($arrItemLinkedWithMarketplace,',').') ',' order by code asc',' limit 1' , $hasQOHOnly);
//$rsUnlistedItem = array_column($rsUnlistedItem,'pkey');

foreach($rsUnlistedItem as $itemRow){
    
    $rsItemImage = $item->getItemImage($itemRow['pkey']);
    if(!empty($rsItemImage)){ 
        echo $itemRow['code'].' OK<br>';
        $lazada->createProduct($itemRow['pkey']);   
    }else{
        echo $itemRow['code'].' No Image <br>'; 
    }
}
?>