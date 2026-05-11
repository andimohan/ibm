<?php
ini_set ('max_execution_time', '3000'); // 50 menit ??

include_once '../../_config.php'; 
include_once '../../_include-v2.php';

includeClass('Item.class.php');
$item = createObjAndAddToCol( new Item());  

$rsItem = $item->searchData();

foreach($rsItem as $itemRow){
    $rsItemImage = $item->getItemImage($itemRow['pkey']);
    
    foreach($rsItemImage as $imgRow){ 
        if(substr($imgRow['file'], -1) != '.') continue;
        
        $ext = 'jpg';
             
		try{			 
		 	 	if (!$item->oDbCon->startTrans())
					throw new Exception($item->errorMsg[100]);
				
				$sql = 'update item_image set file = \''.$imgRow['file'].$ext.'\' where pkey = ' . $item->oDbCon->paramString($imgRow['pkey']);
			    $item->oDbCon->execute($sql);
			 
                $url = $item->defaultDocUploadPath.'item/'.$itemRow['pkey'].'/'.$imgRow['file'];
                rename($url, $url.$ext);
                
				$item->oDbCon->endTrans();
										  
				
			}catch(Exception $e){
			 
		}			
			
        echo $url.'<br>';
    }
    
}

echo 'done';
die;

?>