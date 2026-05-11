<?php

require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AP.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';
 
function getNewObj(){ return  new AP(); } 
$OBJ = getNewObj();
$supplier = new Supplier();
$customer = new Customer();

$paymentDetail = array( 
    'pkey' => array('paramName' => 'key', 'return' => array('isReturn' => false)),   
    'code' =>   array('paramName' => 'payment_id', 'updatable' => false),  // index harus sesuai dengan nama field "jocode" karena diquerynya jocode
    'trdate'  =>  array('paramName' => 'date', 'updatable' => false, 'return' => array('format' => 'mktime') ),   
    'amount'  =>  array('paramName' => 'amount','updatable' => false),
    'discount'  =>  array('paramName' => 'discount','updatable' => false),
    'taxamount'  =>  array('paramName' => 'tax_article_23','updatable' => false),
);

$API_FIELDS = array_merge(array(
                'code' =>   array('paramName' => 'code'),  
                'trdate'  =>  array('paramName' => 'date', 'updatable' => false, 'return' => array('format' => 'mktime') ),   
                'duedate'  =>  array('paramName' => 'duedate', 'updatable' => false, 'return' => array('format' => 'mktime') ),   
                'supplierkey'  =>  array('paramName' => 'supplier_id','updatable' => false, 'return' => array('paramName' => 'suppliercode'), 'search' => array('field' => $supplier->tableName.'.code') ),  
                'suppliername'  =>  array('paramName' => 'supplier_name','updatable' => false),
                'customerkey'  =>  array('paramName' => 'customer_id','updatable' => false, 'return' => array('paramName' => 'customercode'), 'search' => array('field' => $customer->tableName.'.code') ),  
                'customername'  =>  array('paramName' => 'customer_name','updatable' => false),
                'wocode'  =>  array('paramName' => 'work_order_id','updatable' => false),
                'socode'  =>  array('paramName' => 'job_order_id','updatable' => false),
                'amountidr'  =>  array('paramName' => 'amount','updatable' => false),
                'outstanding'  =>  array('paramName' => 'outstanding','updatable' => false),  
                'payment_detail' =>  array('paramName' => 'payment_detail', 'updatable' => false, 'detail' =>  $paymentDetail)
            ),$API_FIELDS); 
   

$RETURN_FIELDS = array(       
                'statuscolor' =>  array('paramName' => 'status_color'),  
            );


require_once '_process.php';
     
?>