<?php
include_once '../_config.php';  
include_once '../_include-v2.php';


includeClass(array('ItemReceiving.class.php','ItemMovement.class.php')); 

$itemReceiving = new ItemReceiving(); 
$itemMovement = new ItemMovement(); 
//$arPayment = new ARPayment(); 

$rsData = $itemReceiving->searchData('','',true,' and '.$itemReceiving->tableName.'.statuskey in (2,3)');
$arrKey = array_column($rsData, 'pkey');
$rsDetail = $itemReceiving->getDetailWithRelatedInformation($arrKey);
$rsDetail = $itemReceiving->reindexDetailCollections($rsDetail,'refkey');

foreach($rsData as $data){
	 $result = array();
	 try{   
                $id = $data[$i];
                $detail = $rsDetail[$id];
                 $note = $data['code'] . '. ' . ucfirst($itemReceiving->lang['itemReceiving']) . ' ' . $itemReceiving->lang['from'] . ' ' . $data['suppliername'];
				$itemReceiving->oDbCon->startTrans(true);
				for ($i = 0; $i < count($detail); $i++) {
                    $itemMovement->updateItemMovement(array(
                        'refkey' => $id,
                        'refkey2' => $id,
                        'refdetailkey' => $detail[$i]['pkey']
                    ), $detail[$i]['itemkey'], $detail[$i]['qty'], 0, $itemReceiving->tableName, array('warehousekey' => $data['warehousekey'], 'warehouselayoutkey' => $data['warehouselayoutkey']), $note, $data['trdate']);
                }
				$itemReceiving->oDbCon->endTrans();   

        } catch(Exception $e){
		 	$itemReceiving->oDbCon->rollback();
		 	
        }	 
	
	
}


echo 'done';
?>