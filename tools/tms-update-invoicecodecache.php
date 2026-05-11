<?php
 
die("die, comment open for reset transaction");

include_once '../_config.php'; 
include_once '../_include-v2.php';

try{  
    if(!$class->oDbCon->startTrans(true))
        throw new Exception($class->errorMsg[100]);
 
    
    $sql = 'select * from sales_order_invoice_receipt_header';
    $rs =  $class->oDbCon->doQuery($sql);
    
    foreach($rs as $row){
        $sql = 'select invoicekey from sales_order_invoice_receipt_detail where refkey =  ' . $row['pkey'];
        $rsDetail =  $class->oDbCon->doQuery($sql);
        
        $sql = 'select code from trucking_service_order_invoice_header where pkey in ('.$class->oDbCon->paramString(array_column($rsDetail,'invoicekey'),',').') ';
        $rsInvoice =  $class->oDbCon->doQuery($sql);
        
        $sql = 'update sales_order_invoice_receipt_header set invoicecodecache = '. $class->oDbCon->paramString(implode(', ', array_column($rsInvoice,'code'))). ' 
                where pkey = ' . $row['pkey'];
        $class->oDbCon->execute($sql);
    }
    
    $class->oDbCon->endTrans(); 

} catch(Exception $e){   
    $class->oDbCon->rollback(); 
}		 


echo 'done';
die;

?>