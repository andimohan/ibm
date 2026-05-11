<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';
   
$shopId = $tokopedia->shopId;

// import category dr tokopedia    
$arrCategory = array();

$page = 1;
$perPage = 50;
$loop = true;

while($loop){
 
    $url = $tokopedia->url . 'inventory/v1/fs/'.$tokopedia->fsid.'/product/info?shop_id='.$shopId.'&page='.$page.'&per_page='.$perPage.'&sort=1'; 
    $result = $tokopedia->execute($url);
    $result = $result['data'];
   
    foreach($result as $itemRow){  
        $childCategory = $itemRow['categoryTree'][count( $itemRow['categoryTree']) -1];
        
        $categoryId = $childCategory['id'];
        $categoryName = $childCategory['name'];
        
        if(!isset($arrCategory[$categoryId]))
            $arrCategory[$categoryId] = array();
        
        $arrCategory[$categoryId]['name'] = $categoryName;
        $arrCategory[$categoryId]['etalaseid'] = $itemRow['menu']['id'];
        
    }
        
    if(count($result) < $perPage)
        $loop=false;
    
    $page++; 
    
}

$errMsg = array(); 
$errMsg['itemSuccess'] = array();
$errMsg['itemFailed'] = array();

 foreach($arrCategory as $key=>$row){ 
       
    // add sebagai kategori
     
    $itemCategory->setLog('etalase : ' .$row['etalaseid'],true, 'category');
    $rsEtalase = $tokopedia->getMarketplaceStorefront('',$tokopedia->marketplaceKey,' and marketplacestorefrontkey = '.$tokopedia->oDbCon->paramString($row['etalaseid'])  );
    $etalaseKey = (!empty($rsEtalase)) ? $rsEtalase[0]['pkey'] : 0;
     
    $arrParam = array();
    $arrParam['code'] = 'xxxx';
    $arrParam['name'] = html_entity_decode($row['name']);
    $arrParam['orderList'] = 0;
    $arrParam['selCategory'] = 0;
    $arrParam['isLeaf'] = 1;
    $arrParam['selStatus'] = 1;
    $arrParam['hidTotalRows'] = array('0' => 1, '1' => 1); 
     
    $arrParam['hidDetailKey'] = array(0);
    $arrParam['hidMarketplaceKey'] = array($tokopedia->marketplaceKey); 
    $arrParam['hidMarketplaceCategoryKey'] = array($key); 
    
    $arrParam['hidDetailStorefrontKey'] = array(0);
    $arrParam['hidStorefrontKey'] = array($etalaseKey);   

    
    // start transatcion  
     try{
    
    		if(!$itemCategory->oDbCon->startTrans(true))
    			throw new Exception($itemCategory->errorMsg[100]);
    			 
            $result = $itemCategory->addData($arrParam);  
               
            if (!$result[0]['valid']) {
                array_push($errMsg['itemFailed'],$arrParam['name'].'. '.$result[0]['message']);
                
                // perlu throw agar connectionCtr nya bisa diset 0 lg. 
                throw new Exception($itemCategory->errorMsg[$result[0]['message']]);
            }else { 
                array_push($errMsg['itemSuccess'],$arrParam['name'].'. '.$result[0]['message']); 
            }
    		            
    	    $itemCategory->oDbCon->endTrans(); 
        
    }catch(Exception $e){
    	$itemCategory->oDbCon->rollback(); 
    }	
        
 }

 
$itemCategory->setLog('Kategori gagal import',true,'category');
foreach($errMsg['itemFailed'] as $row)
    $itemCategory->setLog($row,true,'category');
  
$itemCategory->setLog('Kategori berhasil diimport',true,'category');
foreach($errMsg['itemSuccess'] as $row)
    $itemCategory->setLog($row,true,'category');


echo '<br><br>'; 
echo 'done';
die; 
?>