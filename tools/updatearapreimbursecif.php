<?php
  
die("die, comment open for reset transaction");

ini_set ('max_execution_time', '3000'); // 50 menit ??

include_once '../_config.php';  
include_once '../_include-v2.php';


includeClass(array('Service.class.php','EMKLJobOrder.class.php'));
$obj = createObjAndAddToCol(new Service(SERVICE)); 
$emklJobOrder = createObjAndAddToCol(new EMKLJobOrder());	

$arrJobType = array();
$arrJobType[1] = 'Import';
$arrJobType[2] = 'Export';
$arrJobType[3] = 'Domestic';

$arrCostType = array(
   
    '4' => $obj->lang['ARAPReimburse'], 
);

$rsContainer = $emklJobOrder->getLoadContainer();

$rs = $obj->searchDataRow();

try { 

		if(!$obj->oDbCon->startTrans(true))
			throw new Exception($obj->errorMsg[100]);

        $sql = 'delete from item_coa_link where typekey = 4';
        $obj->oDbCon->execute($sql);
    
    
        $sql = array();
        foreach($rs as $row){
            foreach($arrJobType as $jobtypekey => $jobtyperow){

                foreach($rsContainer as $containerRow){

                    foreach($arrCostType as $typekey => $typeRow){
                         array_push($sql, 'insert into item_coa_link (refkey,typekey, categorykey, coakey, eximkey) values('.$row['pkey'].','.$typekey.','.$containerRow['pkey'].', 8974, '.$jobtypekey.')');
                        
//                         echo '$jobtypekey :' . $jobtyperow.'<br>';
//                         echo '$containerRow :' . $containerRow['name'].'<br>';
//                         echo '$arrCostType :' . $typekey.'<br>';
                    }

                }

//               echo '<br><br>';

            }

        }


foreach($sql as $row){
     $obj->oDbCon->execute($row);   
    echo $row.'<br>';
}
    
    
        $obj->oDbCon->endTrans();   
    
}catch(Exception $e){ 

	$obj->oDbCon->rollback();  
}		
    





// 
//
//
//		foreach($rsItem as $itemRow)  
//		  foreach($rsWarehouse as $warehouseRow)  
//				 $obj->updateItemInWarehouse($itemRow['pkey'],$warehouseRow['pkey']);
//	 
//		$obj->oDbCon->endTrans();   
//
//} catch(Exception $e){ 
//
//	$obj->oDbCon->rollback();  
//}		



echo 'done';
die; 
?>