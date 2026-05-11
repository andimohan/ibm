<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';

die;


//SHOPEE
// DIGUNAKAN JIKA SUDAH AD MASTER ITEM DI SISTEM
// cek item, kalo sudah ad, update attributenya
 
// ambil semua item dr shopee
 

// loop item
$arrNonExists = array();

$loop = true;
$offset = 0;
$itemPerPage = 100;


$testCtr = 0;

while($loop){ 

    $arrPayload = array( 
                     'pagination_offset' => $offset,
                     'pagination_entries_per_page' => $itemPerPage
                    );   


    $payload = $shopee->createJsonBody($arrPayload);

    $response = $shopee->executeRequest('items/get', $payload);
    $response = json_decode($response,true);
    /*$shopee->setLog($response,true,'sh');
    die;*/

    // loop

    // cek item sudah ada atau blm, berdasarkan kode SKU
    $itemsRow = $response['items'];

    foreach($itemsRow as $itemRow){ 
         try{  

                if(!$class->oDbCon->startTrans(true))
                    throw new Exception($class->errorMsg[100]); 


                $itemMarketplaceId = $itemRow['item_id'];

                // get item details
                $arrPayload = array( 'item_id' => intval($itemMarketplaceId) );    
                $payload = $shopee->createJsonBody($arrPayload);

                $itemDetail = $shopee->executeRequest('item/get', $payload);
                $itemDetail = json_decode($itemDetail,true);

                /*echo '<pre>';
                print_r($itemDetail);
                echo '</pre>';

                die;*/

                if(!isset($itemRow['item_sku']) || empty($itemRow['item_sku'])){
                    array_push($arrNonExists,$itemDetail['item']['name'] );  
                    continue;
                }

                //$rsItem = $item->searchData($item->tableName.'.code',$itemRow['item_sku']);

                $rsItem = $item->searchDataRow( 
                            array(  $item->tableName.'.pkey', $item->tableName.'.code' , $item->tableName.'.categorykey'  ) , 
                            ' and '.$item->tableName.'.code = '.$class->oDbCon->paramString($itemRow['item_sku'])
                   ); 


                $itemkey = $rsItem[0]['pkey'];

                if(empty($rsItem)){
                    array_push($arrNonExists,$itemDetail['item']['name'] );  
                    continue;
                }


                //kalo sudah ad update 
                //KATEGORI 

                $sql = 'select * from item_category_marketplace_detail where refkey = '. $class->oDbCon->paramString($rsItem[0]['categorykey']) .' and marketplacekey = '. $class->oDbCon->paramString($shopee->marketplaceKey) ;
                $rsCat  =  $class->oDbCon->doQuery($sql);

                if(empty($rsCat)){ 
                    $sql = 'insert into 
                                item_category_marketplace_detail(refkey,marketplacekey,marketplacecategorykey) 
                            values (
                                '. $class->oDbCon->paramString($rsItem[0]['categorykey']) .',
                                '. $class->oDbCon->paramString($shopee->marketplaceKey) .',
                                '. $class->oDbCon->paramString($itemDetail['item']['category_id'] ) .'
                             ) '; 
                    $class->oDbCon->execute($sql);  
                }

                $attrs = $itemDetail['item']['attributes'];

                //MERK
                // kalo gk ad merk nya, daftarin merk di sistem
                // langsung add merk detail jg
                foreach($attrs as $attr){

                     $attributeValue = $attr['attribute_value'];
                     $attributeId = $attr['attribute_id'];

                     if (strtolower($attr['attribute_name']) == 'merek'){

                         $rsBrand = $brand->searchData($brand->tableName.'.name', $attributeValue);

                         if(empty($rsBrand)){
                            $arrParam = array();
                            $arrParam['code'] = 'xxxx';
                            $arrParam['name'] = $attributeValue; 
                            $newBrand = $brand->addData($arrParam); 
                            $brand->setLog($arrParam,true,'shopee');
                             
                            $newBrand = $newBrand[0]['data'];
                            $brandkey = $newBrand['pkey'];
                         }else{ 
                            $brandkey = $rsBrand[0]['pkey'];
                         }

                         $sql = 'select * from brand_marketplace_detail where refkey = '. $class->oDbCon->paramString($brandkey) .' and marketplacekey = '. $class->oDbCon->paramString($shopee->marketplaceKey) ;
                         $rsBrandDetail =  $class->oDbCon->doQuery($sql);

                        if(empty($rsBrandDetail)){ 
                            $sql = 'insert into 
                                        brand_marketplace_detail(refkey,marketplacekey,marketplacebrandname) 
                                    values (
                                        '. $class->oDbCon->paramString($brandkey) .',
                                        '. $class->oDbCon->paramString($shopee->marketplaceKey) .',
                                        '. $class->oDbCon->paramString($attributeValue) .'
                                     ) '; 
                            $class->oDbCon->execute($sql);  
                        }

                        // update merk ke item
                        $sql = 'update item set brandkey = '. $class->oDbCon->paramString($brandkey) .' where pkey = '. $class->oDbCon->paramString($itemkey); 
                        $class->oDbCon->execute($sql);  

                     }else{

                        $sql = 'select * from item_category_marketplace_attributes where refkey = '. $class->oDbCon->paramString($itemkey) .' and attributekey =  '. $class->oDbCon->paramString($attributeId).' and marketplacekey = '. $class->oDbCon->paramString($shopee->marketplaceKey) ;
                        $rsAttr =  $class->oDbCon->doQuery($sql);

                        if(empty($rsAttr)){
                              $sql = 'insert into 
                                        item_category_marketplace_attributes(refkey,marketplacekey,attributekey,value) 
                                    values (
                                        '. $class->oDbCon->paramString($itemkey) .',
                                        '. $class->oDbCon->paramString($shopee->marketplaceKey) .',
                                        '. $class->oDbCon->paramString($attributeId) .',
                                        '. $class->oDbCon->paramString($attributeValue) .'
                                     ) '; 
                              $class->oDbCon->execute($sql);  
                        }

                     }
                }

                // marketplacelink
                $shopee->addItemMarketplaceLink($itemkey,$itemMarketplaceId); // ini bukan chkbox sync

                $sql = 'delete from  item_marketplace_sync_detail where refkey = '.$shopee->oDbCon->paramString($itemkey).' and marketplacekey = '. $shopee->oDbCon->paramString($shopee->marketplaceKey);
                $class->oDbCon->execute($sql); 

                $sql = 'INSERT into item_marketplace_sync_detail (refkey,marketplacekey,issync) VALUES   ('.$class->oDbCon->paramString($itemkey).', '. $shopee->oDbCon->paramString($shopee->marketplaceKey).',1)';
                $class->oDbCon->execute($sql); 

                $class->oDbCon->endTrans();   

             } catch(Exception $e){
                 $class->oDbCon->rollback();  
             }	
    }


    $loop = $response['more']; 
    $offset += $itemPerPage; 

}


foreach($arrNonExists as $row){
    echo $row.'<br>';
}

echo 'done';
 

?>