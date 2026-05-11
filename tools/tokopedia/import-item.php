<?php
ini_set ('max_execution_time', '3000'); // 50 menit ??

include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';

// DIGUNAKAN UNTUK MENAMBAH ITEM DARI TOKOPEDIA 
// 1. import-etalase
// 2. import-item-category
// 3. import-item 

$obj = $item;

$shopId = $tokopedia->shopId; 
$sql = 'select * from '.$itemCategory->tableMarketplaceCategory.' where marketplacekey = ' . $tokopedia->oDbCon->paramString($tokopedia->marketplaceKey);
$rsCategory = $tokopedia->oDbCon->doQuery($sql);
$rsCategory = array_column($rsCategory, 'refkey', 'marketplacecategorykey');

$rsItem = $obj->searchData();
$rsExistingItem = array_column($rsItem,'code');

$loop = true;
$page = 1;
//$pageTo = 1; // kalo pake batas
$perPage = 50;
$itemCtr = 1; 
 
$errMsg = array();
$errMsg['skuNotFound'] = array();
$errMsg['itemExisted'] = array();
$errMsg['itemSuccess'] = array();
$errMsg['itemFailed'] = array();

while($loop){ 
    
    $url = $tokopedia->url . 'inventory/v1/fs/'.$tokopedia->fsid.'/product/info?shop_id='.$shopId.'&page='.$page.'&per_page='.$perPage.'&sort=1';
    $result = $tokopedia->execute($url);
    $result = $result['data']; 
       
    foreach($result as $itemRow){ 
 
			$itemBasic = $itemRow['basic']; 

            if(!isset($itemRow['other']['sku'])){
                array_push($errMsg['skuNotFound'],$itemBasic['name']);
                continue;
            }
     
            $itemCode = $itemRow['other']['sku'];
            if (in_array($itemCode, $rsExistingItem)){ 
                array_push($errMsg['itemExisted'],$itemBasic['name']);
                continue;
            }
            
            $arrParam = array();
            $arrParam['code'] = $itemCode ;
            $arrParam['name'] = html_entity_decode($itemBasic['name']);
            $arrParam['shortdescription'] = html_entity_decode($itemBasic['shortDesc']);
            $arrParam['hidCategoryKey'] = $rsCategory[$itemBasic['childCategoryID']];
            $arrParam['gramasi'] =  $itemRow['weight']['value'];
            $arrParam['selWeightUnit'] = ($itemRow['weight']['unit'] == 1) ? 3 : 2;
            $arrParam['selBaseUnitKey'] = 1;
            $arrParam['minStockQty'] = 0;
            $arrParam['maxStockQty'] = 0;
            $arrParam['sellingPrice'] = $itemRow['price']['idr'];
            $arrParam['selStatus'] = 1;
            $arrParam['selCondition'] = 1;
            
            $arrImages = array();
            foreach($itemRow['pictures'] as $picRow){ 
                $imgUrl = $picRow['OriginalURL'];
                $fileName = $picRow['fileName'];
    
                $saveToPath = $obj->uploadTempDoc.'item/'.$itemCtr.'/';
                mkdir($saveToPath, 0755, true); 
    
                $saveTo = $saveToPath.$fileName;
                grab_image($imgUrl,$saveTo);
    
                array_push($arrImages,$fileName);
            }
    
            if (!empty($arrImages)){ 
                $imagesList = implode(',',$arrImages) ; 
                $arrParam['token-item-image-uploader'] = $itemCtr;
                $arrParam['item-image-uploader'] = $imagesList;
            }
     
         // start transatcion  
         try{
	
    			if(!$obj->oDbCon->startTrans(true))
    				throw new Exception($obj->errorMsg[100]);
    				
                $newItem = $obj->addData($arrParam);   
        
                if (!$newItem[0]['valid']) {
                    array_push($errMsg['itemFailed'],$itemBasic['name'].'. '.$newItem[0]['message']);
                    
                    // perlu throw agar connectionCtr nya bisa diset 0 lg. 
                    throw new Exception( $newItem[0]['message'] );
                }else { 
                    array_push($errMsg['itemSuccess'],$itemBasic['name'].'. '.$newItem[0]['message']); 
                }
                    
                // add marketplace link
                $itemkey = $newItem[0]['data']['pkey'];
                $newCode = $newItem[0]['data']['code'];
                $productId = $itemBasic['productID'];
                 
                // ini bukan chkbox sync
                $tokopedia->addItemMarketplaceLink($itemkey,$productId); 
              
                // kayayna bagusan setelah diadd br update sync checkbox, agar gk ke sync balik ke tokped
                // update checkbox sync
                $sql = 'delete from  item_marketplace_sync_detail 
                        where 
                        marketplacekey = '. $tokopedia->oDbCon->paramString($tokopedia->marketplaceKey).'
                        and refkey = ' . $tokopedia->oDbCon->paramString($itemkey);
                $obj->oDbCon->execute($sql); 
         
               
                 $sql = ' INSERT into item_marketplace_sync_detail (refkey,marketplacekey,issync)
                        VALUES   ('.$obj->oDbCon->paramString($itemkey).', '. $tokopedia->oDbCon->paramString($tokopedia->marketplaceKey).',1)';
        
                $obj->oDbCon->execute($sql);  
                 
                $itemCtr++;
				            
			    $obj->oDbCon->endTrans(); 
            
		}catch(Exception $e){
			$obj->oDbCon->rollback(); 
		}	
		 
    }
   
    if(count($result) <  $perPage)
        $loop = false; 
    
    /*if($page >= $pageTo )
      $loop = false;*/
      
   $page++;
}
 
$obj->setLog('Item gagal import',true,'import');
foreach($errMsg['itemFailed'] as $row)
   $obj->setLog($row,true,'import');

 
$obj->setLog('SKU tidak ditemukan',true,'import');
foreach($errMsg['skuNotFound'] as $row)
   $obj->setLog($row,true,'import');


/*echo '<br><br>'; 
echo '<b>Item telah terdaftar</b><br>';
foreach($errMsg['itemExisted'] as $row)
    echo $row .'<br>';*/
 
$obj->setLog('Item berhasil diimport',true,'import');
foreach($errMsg['itemSuccess'] as $row)
    $obj->setLog($row,true,'import');
 
echo 'done';
die; 


function grab_image($url,$saveto){
    $ch = curl_init ($url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
    $raw=curl_exec($ch);
    curl_close ($ch);
    if(file_exists($saveto)){
        unlink($saveto);
    }
    $fp = fopen($saveto,'x');
    fwrite($fp, $raw);
    fclose($fp);
}

?>