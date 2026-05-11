<?php
include_once '../../_config.php'; 
include_once '../../_include-v2.php';

set_time_limit(1800); // 30 mins

includeClass(array('Item.class.php'));
$item = new Item();
$itemCategory = new ItemCategory();
$brand = new Brand();
$itemUnit = new ItemUnit();
  
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


// KD ITEM
$arrKDItem = json_decode($response,true);

// select semua kode item agar bisa tentuin mau update / add
$rsItem = $item->searchDataRow(array('pkey','code', 'modifiedon'));
$arrExistingItem = array_column($rsItem,null,'code');
$arrExistingItemCode =  array_column($rsItem,'code');

$rsItemCategory = $itemCategory->searchDataRow(array('pkey','lower(name) as name'));
$arrExistingItemCategory =  array_column($rsItemCategory,null,'name');
$arrExistingItemCategoryName =  array_column($rsItemCategory,'name');

$rsBrand = $brand->searchDataRow(array('pkey','lower(name) as name'));
$arrExistingBrand =  array_column($rsBrand,null,'name');
$arrExistingBrandName =  array_column($rsBrand,'name');

$rsItemUnit = $itemUnit->searchDataRow(array('pkey','lower(name) as name'));
$arrExistingItemUnit =  array_column($rsItemUnit,null,'name');
$arrExistingItemUnitName =  array_column($arrExistingItemUnit,'name');
 
foreach($arrKDItem as $row){
	
	try{		   
			 	
			if (!$class->oDbCon->startTrans(true))
				throw new Exception($class->errorMsg[100]);

			$item = new Item();
			
			$row['ITEM_CATEGORY'] = strtolower($row['ITEM_CATEGORY']);
			$row['BRAND'] = strtolower($row['BRAND']);
			$row['UNIT'] = strtolower($row['UNIT_OF_MEASURE']);
		
			if(!in_array($row['ITEM_CATEGORY'] ,$arrExistingItemCategoryName)){
				echo '<div style="color:#F00">Kategori tidak ditemukan : ' . $row['ITEM_CATEGORY'].'</div>';
				continue;
			}
		 
			if(!in_array($row['UNIT'] ,$arrExistingItemUnitName)){
				echo '<div style="color:#F00">Unit tidak ditemukan : ' . $row['UNIT'].'</div>';
				continue;
			}
		 
			if(!in_array($row['ITEM_NUMBER'],$arrExistingItemCode)) { 
				//echo '<div style="color:#333">Add New Item <b>' . $row['ITEM_NAME'].'</b></div>';
				$arrData = array();
				$arrData['code'] = $row['ITEM_NUMBER'];
				$arrData['name'] = $row['ITEM_NAME']; 
				$arrData['selStatus'] =1;
				$arrData['selCondition'] = 8001;
				$arrData['hidBrandKey'] = (isset($arrExistingBrand[$row['BRAND']])) ? $arrExistingBrand[$row['BRAND']]['pkey'] : 0;
				$arrData['hidCategoryKey'] = $arrExistingItemCategory[$row['ITEM_CATEGORY']]['pkey'];
				$arrData['selBaseUnitKey'] = $arrExistingItemUnit[$row['UNIT']]['pkey'];
				$arrData['sellingPrice'] = $row['PRICE'];
				
				$response = $item->addData($arrData);
				if($response[0]['valid'] == false)
					echo '<div style="color:#F00">'.$response[0]['message'].'</div>';
				
			}else{
				//echo '<div style="color:#333">Update Item <b>' . $row['ITEM_NAME'].'</b></div>';
				$arrData = array();
				$arrData['hidId'] = $arrExistingItem[$row['ITEM_NUMBER']]['pkey'];
				$arrData['hidModifiedOn'] = $arrExistingItem[$row['ITEM_NUMBER']]['modifiedon'];
				$arrData['code'] = $row['ITEM_NUMBER'];
				$arrData['name'] = $row['ITEM_NAME']; 
				$arrData['selStatus'] =1;
				$arrData['selCondition'] =8001;
				$arrData['hidBrandKey'] = (isset($arrExistingBrand[$row['BRAND']])) ? $arrExistingBrand[$row['BRAND']]['pkey'] : 0;
				$arrData['hidCategoryKey'] = $arrExistingItemCategory[$row['ITEM_CATEGORY']]['pkey'];
				$arrData['selBaseUnitKey'] = $arrExistingItemUnit[$row['UNIT']]['pkey'];
				$arrData['sellingPrice'] = $row['PRICE'];
				  
				$response = $item->editData($arrData);
				if($response[0]['valid'] == false)
					echo '<div style="color:#F00">'.$response[0]['message'].'</div>';
				
			}

			$class->oDbCon->endTrans();

			$class->addErrorList($arrayToJs,true,$class->lang['dataHasBeenSuccessfullyUpdated']);    


		}catch(Exception $e){
			$class->oDbCon->rollback();
			$class->addErrorList($arrayToJs,false, $e->getMessage()); 
	}
	
	
}

echo 'done';
die; 
?>