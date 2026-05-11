<?php
 
die("die, comment open for reset transaction");

ini_set ('max_execution_time', '3000'); // 50 menit ??

include_once '../_config.php';  
include_once '../_include-v2.php';

includeClass(array('ItemMovement.class.php','Item.class.php'));
$obj = new ItemMovement(); 

$sql = 'select * from warehouse';
$rsWarehouse = $obj->oDbCon->doQuery($sql);


$sql = 'select * from item where statuskey = 1';
$rsItem = $obj->oDbCon->doQuery($sql);

try { 

		if(!$obj->oDbCon->startTrans(true))
			throw new Exception($obj->errorMsg[100]);

		foreach($rsItem as $itemRow)  
		  foreach($rsWarehouse as $warehouseRow)  
				 $obj->updateItemInWarehouse($itemRow['pkey'],$warehouseRow['pkey']);
	 
		$obj->oDbCon->endTrans();   

} catch(Exception $e){ 

	$obj->oDbCon->rollback();  
}		



echo 'done';
die; 
?>