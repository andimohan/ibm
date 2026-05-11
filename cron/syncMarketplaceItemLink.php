<?php     
require_once '../_config.php'; 
require_once "../_include.php"; 

$class->oDbCon->startTrans();

// USE THIS FOR :
// SEARCH FOR DUPLICATE LINK
// SEARCH FOR UNSYNC link
// REMOVE FROM UNSYNC LINK


$obj = $lazada;
 
// get all products
$rsMP = $obj->getProducts();
$arrExistingCode = array_column($rsMP,null,'SellerSku');

$rsItem = $item->searchData($item->tableName.'.statuskey', 1, true);

$rsLink = $obj->searchLinkItem(); 

// SEARCH FOR DUPLICATE LINK
$sql = 'select 
            count(marketplaceskukey) as total, 
            '.$obj->tableItemMarketplaceLink.'.marketplaceskukey,
            '.$item->tableName.'.code as itemname,
            '.$item->tableName.'.code as itemcode
        from 
            '.$obj->tableItemMarketplaceLink.', 
            '.$item->tableName.'
        where 
            '.$obj->tableItemMarketplaceLink.'.refkey = '.$item->tableName.'.pkey and
            marketplacekey = ' . $obj->oDbCon->paramString($obj->marketplaceKey).'
            group by marketplaceskukey having total > 1
        ' ;
$rs = $obj->oDbCon->doQuery($sql);

if(!empty($rs))
    $obj->setLog('Found dulicate lnnks :');
    
foreach($rs as $row){ 
    $obj->setLog($row['itemcode'] .', '.$row['itemname'].' : '. $row['total']);
    
    //hapus item yg sudah gk ada ata ugk aktif
    $obj->setLog('removing ' . $row['marketplaceskukey'].' link');
    $obj->deleteItemMarketplaceLink('','',$row['marketplaceskukey']);
    $obj->oDbCon->execute($sql);
}

// SEARCH FOR UNSYNC link 
// reselect all links
$rsLink = $obj->searchLinkItem();
    
// FROM MP to MINERVA
$rsLinkItemKeyColl = array_column($rsLink, 'refkey');
$rsItemCodeColl = array_column($rsItem,null,'code');

foreach($arrExistingCode as $sellerSKU=>$itemMP){ 
    $itemkey = $rsItemCodeColl[$sellerSKU]['pkey']; 
    if (!in_array($itemkey, $rsLinkItemKeyColl)){ 
        if (!isset($itemkey) | empty($itemkey)){ 
            // ini ahrus keluar informasi, 
            // kode di MP harus diupdate, karena kode di MINERVA sudah gk non aktif / gk ad lg
            $obj->setLog('missing itemkey : '. $itemkey.', code : '.$sellerSKU);
        }else{ 
            $obj->setLog('adding itemkey : '. $itemkey.', code : '.$sellerSKU);
            $obj->addItemMarketplaceLink($itemkey, $itemMP['item_id'], $itemMP['ShopSku']);
        }     
    }
}

// FROM MINERVA to MP
$rsLinkItemKeyColl = array_column($rsLink, 'marketplaceskukey');
$arrMPExistingCode = array_column($rsMP,'ShopSku');

foreach($rsLinkItemKeyColl as $itemsku){
    if(!in_array($itemsku, $arrMPExistingCode)){
        $obj->setLog('not found : ' . $itemsku);
        $obj->setLog('removing ' . $itemsku .' link');
        $obj->deleteItemMarketplaceLink('','',$itemsku);
    }
}

 $class->oDbCon->endTrans();


?>