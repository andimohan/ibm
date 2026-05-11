<?php 
/*
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
//header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
$ACTION = $_SERVER['REQUEST_METHOD'];  
parse_str(file_get_contents("php://input"),$_RESPONSE);

if(empty($_RESPONSE)) die;

$arrKeys = array_keys($_RESPONSE);
$arrKeys = $arrKeys[0];
 
$shopId = 0;

// manual aj dulu, kayanya webhooknya error dr tokped kurang }
$arrExplode = explode(',',$arrKeys); 
foreach($arrExplode as $row){
   if(strpos( $row , '"shop_id"') !== false){ 
    $shopId = str_replace('"shop_id":','',$row);
    break;
   } 
}

require_once  '../../_mp-client.php';
 
if(!isset(MP_TP_CLIENT[$shopId])) die;
 
$_SERVER['HTTP_HOST'] = MP_TP_CLIENT[$shopId];

require_once '_global.php';  
includeClass(array('Item.class.php'));
$item = new Item();
     */
//$item->setLog($_SERVER['HTTP_HOST'],true,'domain');

//$item->setLog($_RESPONSE,true,'domain');
/*
foreach($_RESPONSE as $responseRow){ 
     
     foreach($responseRow as $key=>$row){ 
            $key = json_decode($key,true); 
    
            $productId = $key['product_id'];
   
           //cari itemkey nya...
           $itemResult = $tokopedia->getProductByProductId($productId);
           
           if(empty($itemResult)) continue; 
            
           $itemCode = $itemResult[0]['other']['sku'];
           $rsItem = $item->searchData($item->tableName.'.code',$itemCode,true);
           if(empty($rsItem)) continue;
              
           $tokopedia->addItemMarketplaceLink($rsItem[0]['pkey'],$productId); 
         
     }
       
}*/
 
?>