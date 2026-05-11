<?php   
 
include_once '../_config.php';  
include_once '../_include-v2.php';

if( ! in_array(DOMAIN_NAME, array('thmsrv.local', 'mandy.wintera.co.id') ) ) die;
 



try{ 
    $class->oDbCon->startTrans();
    
    // map ulang pkey TMT / invoice 
    $sql = 'update sales_order_invoice_receipt_detail, trucking_service_order_invoice_header   set sales_order_invoice_receipt_detail.invoicekey = trucking_service_order_invoice_header.pkey where  sales_order_invoice_receipt_detail.codecache = trucking_service_order_invoice_header.code';
    $class->oDbCon->execute($sql);
    
    // hapus detail yg TMT nya blm terbentuk 
    $sql = 'delete from sales_order_invoice_receipt_detail where codecache not in (select code from trucking_service_order_invoice_header)';
    $class->oDbCon->execute($sql);
    
    // mapping ulang pkey customer 
    $sql= 'update sales_order_invoice_receipt_detail, sales_order_invoice_receipt_header, trucking_service_order_invoice_header  set sales_order_invoice_receipt_header.customerkey =  trucking_service_order_invoice_header.customerkey   where  sales_order_invoice_receipt_header.pkey = sales_order_invoice_receipt_detail.refkey and   sales_order_invoice_receipt_detail.invoicekey = trucking_service_order_invoice_header.pkey';
    $class->oDbCon->execute($sql);
     
    // hapus KWI
    $sql = 'delete from sales_order_invoice_receipt_detail where codecache not like \'TMT%\'';
    $class->oDbCon->execute($sql);
    
    // hapsu header yg gk ad detail
    $sql = 'delete from sales_order_invoice_receipt_header where pkey not in (select refkey from sales_order_invoice_receipt_detail) ';
    $class->oDbCon->execute($sql);
    
    
//    
//    
//    $sql = 'select * from sales_order_invoice_receipt_detail';
//    $rs = $class->oDbCon->doQuery($sql);
    
//    foreach($rs as $row){
//        $invcode = $row['codecache'];
//        $sql = '';
//    }
    
    $class->oDbCon->endTrans(); 
} catch(Exception $e){
    $class->oDbCon->rollback();
    var_dump($e->getMessage()); 
}		


echo 'done';


?>