<?php
include_once '../../_config.php'; 
include_once '../../_include-v2.php';

includeClass(array('Item.class.php','ItemAdjustment.class.php','ItemMovement.class.php','Warehouse.class.php'));
$item = new Item();
$itemAdjustment = new ItemAdjustment();
$warehouse = new Warehouse();
$itemMovement = new ItemMovement();

// call API
$url = 'http://192.140.224.254:9000/api/items/online/std/';
 
$header = array(
	'Content-Type: application/json', 
);

$connection = curl_init(); 
curl_setopt($connection, CURLOPT_URL, $url); 
curl_setopt($connection, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($connection, CURLOPT_SSL_VERIFYHOST, 0);  
//curl_setopt($connection, CURLOPT_POST, 1);  
//curl_setopt($connection, CURLOPT_POSTFIELDS, $payload); 
curl_setopt($connection, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($connection); 
curl_close($connection); 

$warehousekey = $warehouse->getDefaultData();

// KD ITEM
$arrKDItem = json_decode($response,true);

// select semua kode item agar bisa tentuin mau update / add
$rsItem = $item->searchData();
$arrExistingItem = array_column($rsItem,null,'code'); 
$arrExistingItemCode = array_column($rsItem,'code'); 
$arrExistingItemKey = array_column($rsItem,'pkey'); 

$rsQOH = $itemMovement->getItemsQOH($arrExistingItemKey,$warehousekey);
$rsQOH = array_column($rsQOH,null,'itemkey');

$arrData = array();
$arrData['code'] = 'xxxxx';
$arrData['trDate'] = date('d / m / Y');
$arrData['selWarehouseKey'] =  $warehousekey;
$arrData['selStatus'] = 1;

$arrData['hidDetailKey'] = array();
$arrData['hidItemKey'] = array();
$arrData['qtyBefore'] = array();
$arrData['qtyAfter'] = array();
$arrData['qtyAdjust'] = array();
$arrData['baseUnitKey'] = array();
$arrData['unitConvMultiplier'] = array();
$arrData['COGS'] = array();


foreach($arrKDItem as $row){
 	if(!in_array($row['ITEM_NUMBER'],$arrExistingItemCode)) continue;
	
	$itemkey = $arrExistingItem[$row['ITEM_NUMBER']]['pkey'];
	
	$qtyBefore = $rsQOH[$itemkey]['qtyinbaseunit'] ;//$arrExistingItem[$row['ITEM_NUMBER']]['qtyonhand'];
	$qtyAfter = $row['QTY_AVAILABLE'];
	$qtyAdj = $qtyAfter - $qtyBefore;
	
	if($qtyAdj == 0) continue;
		
	array_push($arrData['hidDetailKey'],1);
	array_push($arrData['hidItemKey'],$itemkey);
	array_push($arrData['qtyBefore'],$qtyBefore);
	array_push($arrData['qtyAfter'],$qtyAfter);
	array_push($arrData['qtyAdjust'],$qtyAdj);
	array_push($arrData['baseUnitKey'],$arrExistingItem[$row['ITEM_NUMBER']]['baseunitkey']);
	array_push($arrData['unitConvMultiplier'],1);
	array_push($arrData['COGS'],0); 
}

if(empty($arrData['hidDetailKey'])){
	die('tidak ad perubahan data');
}

try{		   
			 	
		if (!$class->oDbCon->startTrans(true))
			throw new Exception($class->errorMsg[100]);

		$response = $itemAdjustment->addData($arrData);
	
	
		if($response[0]['valid'] == false)
			echo '<div style="color:#F00">'.$response[0]['message'].'</div>';
		else
			echo '<div style="color:#333">'.$response[0]['message'].'</div>';
			
		$response = $itemAdjustment->changeStatus($response[0]['data']['pkey'], 2, '',false,true);
	 
		$class->oDbCon->endTrans();

		$class->addErrorList($arrayToJs,true,$class->lang['dataHasBeenSuccessfullyUpdated']);    


}catch(Exception $e){
		$class->oDbCon->rollback();
		$class->addErrorList($arrayToJs,false, $e->getMessage()); 
}
echo 'done';
die; 
?>