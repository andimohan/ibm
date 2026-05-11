<?php
die("die, comment open for reset transaction");

include_once '../../_config.php'; 
include_once '../../_include-v2.php';

includeClass(array('AR.class.php','TruckingServiceOrderInvoice.class.php','SalesOrderInvoiceReceipt.class.php',  'TruckingInvoiceReceipt.class.php'));
 
$obj = new AR();
$obj->oDbCon->startTrans();

$truckingInvoiceReceipt = new TruckingInvoiceReceipt();

$sql = 'select ' .$truckingInvoiceReceipt->tableName.'.receiveddate , '.$truckingInvoiceReceipt->tableNameDetail.'.invoicekey  
        from ' .$truckingInvoiceReceipt->tableName.', '.$truckingInvoiceReceipt->tableNameDetail.'
        where
            '. $truckingInvoiceReceipt->tableName.'.pkey = '.$truckingInvoiceReceipt->tableNameDetail.'.refkey and
            '. $truckingInvoiceReceipt->tableName.'.statuskey in (2,3)';

 $rsReceipt = $obj->oDbCon->doQuery($sql);


foreach($rsReceipt as $row){
    $invoicekey = $row['invoicekey'];
    $receiveddate = $row['receiveddate'];
    
    $sql = 'update ar set duedate = ' .$obj->oDbCon->paramString($receiveddate).' + interval duedays day where refkey = ' . $obj->oDbCon->paramString($invoicekey);
    $obj->oDbCon->execute($sql);
}


$obj->oDbCon->endTrans();
echo 'done';

?>