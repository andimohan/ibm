<?php 
include_once '../../_config.php';  
include_once '../../_include-v2.php';
   
includeClass(array('Marketplace.class.php','SalesOrder.class.php'));

$lazada = new Lazada();
 
// digunakan utk temp mengecek ulang apakah SKU sudah ngelink ke ID yg benar

try{  

    if(!$class->oDbCon->startTrans())
        throw new Exception($class->errorMsg[100]); 
 	
	$lazada->syncMarketplaceCategoryAttributes(1);
	
    $class->oDbCon->endTrans();   

} catch(Exception $e){
    $class->oDbCon->rollback();  
}	 

foreach($arrNonExists as $row){
    echo $row.'<br>';
}

echo 'done';
 

?>