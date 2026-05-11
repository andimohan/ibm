<?php 

die("die..");

require_once '../../_config.php'; 
require_once "../../_include.php";  
   
// 1219457597 <= disc ID
/*
$today =  date('U', strtotime("+1 minute")); 
$endTime =  date('U',strtotime("+180 days")); 

$payload = $shopee->createJsonBody(array(
    'discount_name' =>  'Promo November',
    'start_time' => intval($today),
    'end_time' => intval($endTime), 
));

$response = $shopee->executeRequest('discount/add', $payload);*/
 
// CAMPAIGN
// 1219457597 <= disc ID
 
// UPDATE HARGA BARANGNYA DULU... (NAIKIN)
/*
$syncCriteria = array();
$syncCriteria['attr'] = array('price'); // sementara
$syncCriteria['type'] = 2;  

foreach($rsItem as $key=>$itemRow){ 
    $syncCriteria['itemkey'] = $itemRow['pkey'];  
    $shopee->syncProducts($syncCriteria);
}
*/


// ADD DISCOUNT ITEM
$itemkey = 1942;

$discountedPrice = $shopee->getItemPrice($itemkey);
$rsItemLink = $shopee->searchLinkItem('', ' and refkey in ('.$shopee->oDbCon->paramString($itemkey).' ) ');
$marketplaceitemkey = $rsItemLink[0]['marketplaceitemkey'];

$arrItem = array();
array_push($arrItem, array(
                'item_id' => intval($marketplaceitemkey),
                'item_promotion_price' => floatval($discountedPrice['discountedprice']),
                'purchase_limit' => 100
            ));

 
$payload = $shopee->createJsonBody(array(
    'discount_id' => '1219457597',
    'items' => $arrItem,  
));

$shopee->setLog($payload,true);

$response = $shopee->executeRequest('discount/items/add', $payload); 
$response = json_decode($response,true); 

var_dump($response);
?>