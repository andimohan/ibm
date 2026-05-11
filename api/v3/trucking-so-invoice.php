<?php

require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderInvoice.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderCategory.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
 
function getNewObj(){ return  new TruckingServiceOrderInvoice(); } 
$OBJ = getNewObj();
$customer = new Customer();
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();

// ini bukan utk update, tp narik dr pembyaran AR
$paymentDetail = array( 
    'paymentkey' => array('paramName' => 'key'),   
    'paymentcode' =>   array('paramName' => 'payment_id', 'updatable' => false),  // index harus sesuai dengan nama field "jocode" karena diquerynya jocode
    'paymentdate'  =>  array('paramName' => 'date', 'updatable' => false, 'return' => array('format' => 'mktime') ),   
    'amount'  =>  array('paramName' => 'amount','updatable' => false),
    'discount'  =>  array('paramName' => 'discount','updatable' => false),
    'taxamount'  =>  array('paramName' => 'tax_article_23','updatable' => false),
);

$paymentChannelDetail = array( 
    'pkey' => array('paramName' => 'key'),   
    'paymentkey'  =>  array('paramName' => 'payment_channel_key'),
    'amount'  =>  array('paramName' => 'amount'), 
);

$invoiceItemDetail = array( 
    'pkey' => array('paramName' => 'key'),    
    //'itemkey' => array('paramName' => 'service_key', 'updatable' => false), 
    //'itemkey' => array('paramName' => 'service_id', 'ref' => array('obj' => new Item(), 'field' => 'code' ), 'return' => array('paramName' => 'itemcode')), 
    'refsodetailkey' => array('paramName' => 'so_detail_key','updatable' => false),    
    'refsorequestid' => array('paramName' => 'so_request_id', 'forceParam' => true, 'updatable' => false),   // request id harus berbeda antara selling trucking dan charges
    'requestid' => array('paramName' =>'request_id'),
    'itemname' => array('paramName' => 'service_name','updatable' => false),   
    'aliasname' => array('paramName' => 'service_alias'),   
    'qtyinbaseunit' => array('paramName' => 'qty'),   
    'priceinunit' => array('paramName' => 'price'),   
    'taxdetail' => array('paramName' => 'vat_percentage'),   
    'taxdetailvalue' => array('paramName' => 'vat_value'),   
    'ispriceincludetax' => array('paramName' => 'vat_include'),   
    'istax23' => array('paramName' => 'tax_article_23')  ,   
    'beforetaxdetailvalue' => array('paramName' => 'total'), 
    'aftertaxdetailvalue' => array('paramName' => 'totalaftertax'), 
    'discountdetailtype' => array('paramName' => 'discount_type'), 
    'discountdetailvalue' => array('paramName' => 'discount_value') 
);

$jobOrderDetail = array( 
    'pkey' => array('paramName' => 'key'),  
    'invoicetype' => array('paramName' => 'transaction_type_key'),  
    'salesorderkey' =>  array('paramName' => 'job_order_id',  'ref' => array('obj' => new TruckingServiceOrder(), 'field' => 'code' ), 'return' => array('paramName' => 'jocode')), 
    'jodate'  =>  array('paramName' => 'date', 'updatable' => false, 'return' => array('format' => 'mktime')),    
    'description' => array('paramName' => 'description'),  
    'service_detail' =>  array('paramName' => 'service_detail', 'dataset' => $OBJ->arrItem,'tableName' => $OBJ->tableNameItemDetail,  'detail' =>  $invoiceItemDetail)
);
   
$API_FIELDS = array_merge(array(
                'trdate'  =>  array('paramName' => 'date', 'updatable' => false, 'return' => array('format' => 'mktime') ),   
                'duedate'  =>  array('paramName' => 'duedate', 'updatable' => false, 'return' => array('format' => 'mktime') ),    
                'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' ), 'return' => array('paramName' => 'warehousecode')), 
                'termofpaymentkey' => array('paramName' => 'term_of_payment_key','mandatory' => true),   
                'companybankkey' => array('paramName' => 'company_bank_key'),
                'vanumber' => array('paramName' => 'va_number'),
                'trdesc' => array('paramName' => 'description'), 
                'customcodekey' => array('paramName' => 'invoice_type_key','mandatory' => true),  
                'invoicetype' => array('paramName' => 'invoice_type_name', 'updatable' => false),  
                'customerkey'  =>  array('paramName' => 'customer_id','updatable' => false,  'ref' => array('obj' => $customer, 'field' => 'code' ) , 'return' => array('paramName' => 'customercode'), 'search' => array('field' => $customer->tableName.'.code') ),  
                'customername'  =>  array('paramName' => 'customer_name','updatable' => false),
                'beforetaxtotal'  =>  array('paramName' => 'beforetaxtotal','updatable' => false),
                'taxvalue'  =>  array('paramName' => 'vat','updatable' => false),
                'grandtotal'  =>  array('paramName' => 'grandtotal','updatable' => false),
                'tax23percentage'  =>  array('paramName' => 'tax_article_23_percentage'),
                'tax23value'  =>  array('paramName' => 'tax_article_23_value','updatable' => false),
                'aroutstanding'  =>  array('paramName' => 'outstanding','updatable' => false),
                'arstatuskey'  =>  array('paramName' => 'ar_status_key','updatable' => false),
                'arstatusname'  =>  array('paramName' => 'ar_status_name','updatable' => false),
                'jobcategoryname'  =>  array('paramName' => 'job_category_name','updatable' => false ),   
                'containernumber'  =>  array('paramName' => 'container_number'),   
                'requestid'  =>  array('paramName' => 'request_id'),   
                'jobcategorycode'  =>  array('paramName' => 'job_category_id', 'return' => array('isReturn' => false), 'updatable' => false,  'search' => array('field' => $truckingServiceOrderCategory->tableName.'.code') ),   
                'job_order_detail' =>  array('paramName' => 'job_order_detail', 'dataset' => $OBJ->arrDataDetail, 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $jobOrderDetail),
                'payment_channel_detail' =>  array('paramName' => 'payment_channel_detail', 'dataset' => $OBJ->arrPaymentDetail,'tableName' => $OBJ->tablePayment, 'detail' => $paymentChannelDetail),
                'payment_detail' =>  array('paramName' => 'payment_detail', 'updatable' => false, 'detail' => $paymentDetail) // nanti perlu ditambahkan tableName
            ),$API_FIELDS); 
   
// tambahan
$RETURN_FIELDS = array(       
                'statuscolor' =>  array('paramName' => 'status_color'),  
            );


require_once '_process.php';
     
?>