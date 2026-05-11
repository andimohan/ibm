<?php    

require_once '../_config.php'; 
require_once "../_include-v2.php"; 

includeClass(array('Asset.class.php','AssetDepreciation.class.php')); 
$asset = new Asset();
$assetDepreciation = new AssetDepreciation();

try{ 
        if(!$assetDepreciation->oDbCon->startTrans())
				throw new Exception($assetDepreciation->errorMsg[100]);


            $rs = $asset->searchData();
            foreach($rs as $row){ 
                $assetDepreciation->updateTotalDepreciatedCtr($row['pkey']); 
            }

			$assetDepreciation->oDbCon->endTrans(); 
		
} catch(Exception $e){
    $assetDepreciation->oDbCon->rollback(); 
}		



echo 'done';
?>