<?php

die("die, comment open for reset transaction");

include_once '../_config.php';  
include_once '../_include-v2.php';

ini_set('max_execution_time', 30000000);
ini_set('memory_limit', '2024M');


includeClass(array('SalesOrder.class.php')); 

$salesOrder = new SalesOrder(); 
//$arPayment = new ARPayment(); 

$rsSalesOrder = $salesOrder->searchData($salesOrder->tableName.'.statuskey',1,true,' limit 10000');


foreach($rsSalesOrder as $row){
	 $salesOrder->startNewErrorLogSession();   
	
	 if ($row['grandtotal'] == 0) continue;
	
	 $result = array();
	
	 try{   
				$salesOrder->oDbCon->startTrans(true);
				$result = $salesOrder->changeStatus($row['pkey'], 2); 
		  
				$salesOrder->oDbCon->endTrans();   

        } catch(Exception $e){
		 	echo $row['code'].'<br>';
		 	foreach($result as $resultRow){
				echo $resultRow['message'].'<br>';
			}
		 
		 	echo '<br><br>';
//		 	echo '<br>';
//		 	echo $e->getMessage();
//		 	echo '<br>';
//        	die;
        }	 
	
	
}

$salesOrder->oDbCon->endTrans();

echo 'done';
?>