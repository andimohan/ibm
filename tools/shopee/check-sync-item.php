<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';
 
// digunakan utk temp mengecek item apa saja yg suda keupload

try{  

    if(!$class->oDbCon->startTrans())
        throw new Exception($class->errorMsg[100]); 
 
    $sql = 'delete from item_marketplace_check where marketplacekey = ' . $class->oDbCon->paramString($shopee->marketplaceKey);
    $class->oDbCon->execute($sql);
    
    // loop item 

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


        // loop

        // cek item sudah ada atau blm, berdasarkan kode SKU
        $itemsRow = $response['items'];

        foreach($itemsRow as $itemRow){ 
            $sql = 'insert into item_marketplace_check (marketplacekey, itemcode) values ('. $class->oDbCon->paramString($shopee->marketplaceKey).','.$class->oDbCon->paramString($itemRow['item_sku']).')';
            $class->oDbCon->execute($sql);    
        }


        $loop = $response['more']; 
        $offset += $itemPerPage;
  
    }

    
    $class->oDbCon->endTrans();   

} catch(Exception $e){
    $class->oDbCon->rollback();  
}	 

foreach($arrNonExists as $row){
    echo $row.'<br>';
}

echo 'done';
 

?>