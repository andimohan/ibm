<?php

require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLPurchaseOrder.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLJobOrder.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Container.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Currency.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php';    
 
$OBJ = new EMKLPurchaseOrder(EMKL['jobType']['import']);
$container = new Container();
$service  = new Service(SERVICE);
$currency = new Currency();

// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
    
$purchaseDetail = array( 
    'pkey' => array('paramName' => 'pkey'),
    'itemkey' => array('paramName' => 'container_name', 'ref' => array('obj' => $container)), 
    'servicekey' => array('paramName' => 'service_name', 'mandatory' => true, 'ref' => array('obj' => $service)), 
    'currencykey' =>  array('paramName' => 'currency', 'mandatory' => true, 'ref' => array('obj' => $currency)), 
    'qty' =>  array('paramName' => 'qty' ,'mandatory' => true),
    'priceinunit' =>  array('paramName' => 'price' ,'mandatory' => true),
    'description' => array('paramName' => 'description')
);

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'), 
    'refkey' =>   array('paramName' => 'sales_order_id', 'mandatory' => true, 'ref' => array('obj' => new EMKLJobOrder(EMKL['jobType']['import']), 'field' => 'code' )), 
    'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' )), 
    'refinvoicecode'  =>  array('paramName' => 'invoice_reference'),  
    'supplierkey'  =>  array('paramName' => 'supplier_id','mandatory' => true, 'ref' => array('obj' => new Supplier(), 'field' => 'code' )), 
    'trdate'  =>  array('paramName' => 'date','mandatory' => true ), 
    'currencykey'  =>  array('paramName' => 'currency','mandatory' => true, 'ref' => array('obj' => $currency)), 
    'rate'  =>  array('paramName' => 'rate','mandatory' => true), 
    'items_detail' =>  array('paramName' => 'items_detail', 'mandatory' => true, 'dataset' => $OBJ->arrDataDetail, 'detail' =>  $purchaseDetail)
));
    
require_once '_process.php';
     
?>