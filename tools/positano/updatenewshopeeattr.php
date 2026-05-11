<?php 
die;

require_once '../../_config.php'; 
require_once "../../_include-v2.php";  
 
includeClass(array('Marketplace.class.php','Brand.class.php','Item.class.php'));
$brand = createObjAndAddToCol(new Brand());  
 
// resync
$shopee = new Shopee();

$sql  = 'select item.pkey, item.code from item,item_marketplace_sync_detail 
where item_marketplace_sync_detail.refkey = item.pkey 
and item_marketplace_sync_detail.marketplacekey = 2 
and issync = 1 
and brandkey = 10
and statuskey = 1
order by item.pkey desc limit 200,100';

$rsItem = $class->oDbCon->doQuery($sql);
$arrItemCode = array_column($rsItem,'code');
echo '<pre>';
print_r($arrItemCode);
echo '</pre>';

$arrPkey = array_column($rsItem,'pkey');

$syncCriteria = array(); 
$syncCriteria['attr'] = array('brand' , 'price',  'others'); // karena kalo stok awal 0, pas brg masuk, harga harus update ulang
$syncCriteria['type'] = 2;  
$syncCriteria['itemkey'] = $arrPkey; 
 
$shopee->syncProducts($syncCriteria);   
 
die("done");


//set_time_limit(1800); // 30 mins
  
$arrAttr = array();
$arrAttr[100134] = array('value' => 'Combed Cotton');
$arrAttr[0] = array('value' => ''); 

foreach($arrAttr as $attrkey=> $attrRow){  
    
    // cari semua item yg link ke shopee dulu
    $sql = 'select item.pkey, item.code,  item.name, item.brandkey, item.categorykey, item.statuskey from item, item_marketplace_sync_detail where item.pkey = item_marketplace_sync_detail.refkey and marketplacekey = 2 and issync = 1';
    $rsItem = $class->oDbCon->doQuery($sql);
    $arrItemKey = array_column($rsItem,'pkey');
  
    $sql = 'select pkey,refkey,value from item_category_marketplace_attributes where refkey in ('.implode(',',$arrItemKey).') and marketplacekey = 2 and attributekey  = '.$attrkey;
    $rsAttr = $class->oDbCon->doQuery($sql);
    $rsAttr = $class->reindexDetailCollections($rsAttr,'refkey');
    
    $ctr = 0;
    echo 'total '.count($rsItem).'<br>';
    
    foreach($rsItem as $itemrow){  
        if($ctr > 6010) die(' ctr '.$ctr);
        $ctr++;
        
        try{			 
		 	 	if (!$class->oDbCon->startTrans(true))
					throw new Exception($class->errorMsg[100]);
			 	 
                $rs = ($rsAttr[$itemrow['pkey']]) ? $rsAttr[$itemrow['pkey']] : array();
                
                if(!empty($rs['value'])) continue;

                // sekalian ngecek
                if(count($rs) > 1)
                    echo 'double ' . $itemrow['code'] .'<br>';

                
                if($attrkey == 0){ 
                    $rsBrand = $brand->getMarketplaceBrand($itemrow['brandkey'],MARKETPLACE['shopee'],$itemrow['categorykey']);   
                    if(empty($rsBrand))
                        echo  $itemrow['code'] . ' - '. $itemrow['name'].'<br>'; 
                      
                    $value = $rsBrand[0]['marketplacebrandname'];
                }else{ 
                    $value = $attrRow['value'];   
                }
            
                // kalo gk ad insert
                if(empty($rs))  { 
                    $sql = 'insert into item_category_marketplace_attributes(refkey,marketplacekey,attributekey,value) 
                            values ('.$itemrow['pkey'].',2,'.$attrkey.',\''.$value.'\')'; 
                    $class->oDbCon->execute($sql);
                }else if (empty($rs[0]['value'])){ 
                    // kalo ad tp kosong, update
                    $sql = 'update item_category_marketplace_attributes set value = \''.$value.'\' where pkey = '.$rs[0]['pkey'];  
                    $class->oDbCon->execute($sql);
                }
 
				$class->oDbCon->endTrans(); 
			}catch(Exception $e){
				$class->oDbCon->rollback(); 
                echo $e->getMessage().'<br>';
                die;
		   }			

 
        // kalo statusnya aktif, update ke marketplace
    }
       
}

echo 'total proses ' . ($ctr-1).'<br>';
echo 'done';
?>