<?php
include_once '../../_config.php'; 
include_once '../../_include-v2.php'; 

includeClass(array('Item.class.php','Marketplace.class.php'));

if(!isset($_GET['itemkey']) || empty($_GET['itemkey'])) die;

$item = new Item();
$marketplace = new Marketplace();

// tembak mati dulu
$tokopedia = $marketplace->getMarketplaceObj(3)[0]['obj'];
 
$itemkey = $_GET['itemkey'];

$rsItem = $item->getDataRowById($itemkey);
$rsLink = $tokopedia->searchLinkItem($itemkey);

if(!empty($rsLink)) die;


// utk relink item yg gagal link karena webhook
$responseItem = $tokopedia->getProductBySKU($rsItem[0]['code']);   
$shopId = $tokopedia->shopId; 

if(!isset($responseItem['basic']) || empty($responseItem['basic']['productID'])) die;

$productId  = $responseItem['basic']['productID'];

$tokopedia->addItemMarketplaceLink($itemkey,$productId);  

echo 'done';
die; 
 
?>