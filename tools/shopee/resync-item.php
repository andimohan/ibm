<?php
include_once '../../_config.php'; 
include_once '../../_include.php';
include_once '../../_global.php';
 
// digunakan utk temp mengecek ulang apakah SKU sudah ngelink ke ID yg benar

try{  

    if(!$class->oDbCon->startTrans())
        throw new Exception($class->errorMsg[100]); 
 
    // link skrg
    $sql = 'select item.code as itemcode, item_marketplace_link.* from item,item_marketplace_link where item.pkey = item_marketplace_link.refkey and marketplacekey = ' . $class->oDbCon->paramString($shopee->marketplaceKey);
    $rsExistingLink = $class->oDbCon->doQuery($sql);
    
    $rsExistingLink = array_column($rsExistingLink,null,'itemcode');
    
    // ambil semua item dr shopee
    
    // loop item 
    $loop = true;
    $offset = 0;
    $itemPerPage = 100;
 
    $testCtr = 0;

    echo '<table>';
    echo '<tr><td>SKU</td><td>ID Lama</td><td>ID Baru</td></tr>';

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

        // kalo blm terdaftar atau beda, munculkan dulu 
        foreach($itemsRow as $itemRow){ 
            $itemSKU = $itemRow['item_sku'];
            $itemID = $itemRow['item_id'];
            
            /*if($itemSKU=='GLDSPRD1'){
                echo '<pre>';
                var_dump($itemRow);
                echo '</pre>';  
            }*/
            
            if( isset($rsExistingLink[$itemSKU]) && $rsExistingLink[$itemSKU]['marketplaceitemkey'] == $itemID ) continue;
            
            echo '<tr><td>'.$itemSKU.'</td><td>'.$rsExistingLink[$itemSKU]['marketplaceitemkey'].'</td><td>'.$itemID.'</td></tr>';
            // nanti baru execute
        }
 
        $loop = $response['more']; 
        $offset += $itemPerPage;
  
    }
        
    echo '</table>'; 

    
    $class->oDbCon->endTrans();   

} catch(Exception $e){
    $class->oDbCon->rollback();  
}	 

foreach($arrNonExists as $row){
    echo $row.'<br>';
}

echo 'done';
 

?>