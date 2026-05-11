<?php    
require_once '../_config.php'; 
require_once '../_include-v2.php';
 
ini_set('max_execution_time', '600'); //300 seconds = 5 minutes

// sementara utk bulanan dulu

includeClass(array('SalesOrder.class.php','SalesOrderRecurringSubscription.class.php'));

$salesOrderRecurringSubscription = new SalesOrderRecurringSubscription();
$salesOrder = new SalesOrder(); 

$interval = $salesOrderRecurringSubscription->invoiceInterval; 

// hanya generate yg blm kebentuk saja sales order nya
$rsSalesOrderRecurring = $salesOrderRecurringSubscription->searchData('','',true,' and '.$salesOrderRecurringSubscription->tableName.'.statuskey in (2)
																 and '.$salesOrderRecurringSubscription->tableName.'.nextrecurringdate = date(now()) + interval '.$salesOrderRecurringSubscription->oDbCon->paramString($interval).' day
																 and  '.$salesOrderRecurringSubscription->tableName.'.pkey not in (
																 	select '.$salesOrder->tableName.'.refsubscriptionkey 
																	from '.$salesOrder->tableName.'
																	where 
																		'.$salesOrder->tableName.'.trdate = date(now()) + interval '.$salesOrderRecurringSubscription->oDbCon->paramString($interval).' day and
                                                                        '.$salesOrder->tableName.'.statuskey in (1,2,3)  
																 ) '); 
  
foreach($rsSalesOrderRecurring as $row){
	
		try{  
           $salesOrderRecurringSubscription->addSalesOrder($row['pkey'],false,date('Y-m-d'));
        }catch (Exception $e){ 
			echo $e->getMessage().'<br>';
        }
 
}

echo 'done';

?>