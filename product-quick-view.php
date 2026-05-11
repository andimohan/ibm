<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';  
   
$warehouseCriteria = ' and iswebqoh = 1';

if(empty($_GET)){
	header("location: /");
	die;
} 

$id = $_GET['id']; 
$rsItem = $item->searchData($item->tableName.'.pkey',$id,true, ' and '.$item->tableName.'.statuskey = 1','','','',$warehouseCriteria); 
if(empty($rsItem)){
	header("location: /");
	die;
}
     
if (IGNORE_QOH) $rsItem[0]['qtyonhand'] = 99999;
  
$rsItemImage = $item->getItemImage($rsItem[0]['pkey']);
$rsItem[0]['mainimage'] = $rsItemImage[0]['file'];	

$arrTwigVar['item'] = $rsItem[0];     
$arrTwigVar['inputQty'] = $class->inputNumber("mnvCartQty", array('value' => 1, 'etc' => 'style="text-align:center"'));     


$criteria = array(); 
$criteria['brandkey'] = $rsItem[0]['brandkey'];
$criteria['itemkey'] = $rsItem[0]['pkey'];
$criteria['itemcategorykey'] = $rsItem[0]['categorykey'];

$voucher = $voucher->getAvailableVoucher(array(VOUCHER_CATEGORY['sales'],VOUCHER_CATEGORY['shipment']),VOUCHER_TYPE['regular'],CUSTOMER_TYPE['enduser'], $criteria );
$arrTwigVar['availableVoucher'] = $voucher;
        
echo $twig->render('product-quick-view.html', $arrTwigVar);
?>