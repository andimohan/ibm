<?php
die('deprecated');

include_once '../../_config.php'; 
include_once '../../_include-v2.php';
include_once '../../_global.php';


// utk relink item yg gagal link karena webhook
 
$shopId = $tokopedia->shopId; 

$sql = 'select 
           '.$item->tableName.'.pkey,
           '.$item->tableName.'.code  
        from 
            '.$item->tableName.' 
        where '.$item->tableName.'.pkey not in (
            select '. $marketplace->tableItemMarketplaceLink.'.refkey from  '. $marketplace->tableItemMarketplaceLink.' where 
            marketplacekey = ' . $tokopedia->oDbCon->paramString($tokopedia->marketplaceKey).'
        )';
 
$rsItemNotLinked = $class->oDbCon->doQuery($sql);
$rsItemNotLinkedKey = array_column($rsItemNotLinked,'pkey','code'); 
$rsItemNotLinkedCode = array_column($rsItemNotLinked,'code');
  
  
$loop = true;
$page = 1;
$perPage = 50;
$itemCtr = 1; 
 
 try{  

    if(!$class->oDbCon->startTrans())
        throw new Exception($class->errorMsg[100]); 
        
        while($loop){
         
            $url = $tokopedia->url . 'inventory/v1/fs/'.$tokopedia->fsid.'/product/info?shop_id='.$shopId.'&page='.$page.'&per_page='.$perPage.'&sort=1';
            $result = $tokopedia->execute($url);
            $result = $result['data']; 
        
            foreach($result as $itemRow){ 
        
                $itemBasic = $itemRow['basic'];  
                
                $productId = $itemBasic['productID'];
        
                if(!isset($itemRow['other']['sku'])){
                    echo '<b>SKU not found</b>, ' .$itemBasic['name'].'<br>';
                    continue;
                }
         
                $itemCode = $itemRow['other']['sku'];
                if (in_array($itemCode, $rsItemNotLinkedCode)){ 
                    echo '<b>Item not linked</b>, ' .$itemBasic['name'].'<br>';  
                    $tokopedia->addItemMarketplaceLink($rsItemNotLinkedKey[$itemCode],$productId);  
                }
                   
                $itemCtr++;
            }
            
            if(count($result) <  $perPage)
                $loop = false;
            
            $page++;
        }
	    
	    $class->oDbCon->endTrans();   

} catch(Exception $e){
    $class->oDbCon->rollback();  
}	 
 
echo 'done';
die; 
 
?>