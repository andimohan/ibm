<?php
require_once '../../_config.php';  
require_once '_include.php';
require_once '_global.php';
 

$obj = null;

$code = $_GET['code'];
$module = $_GET['module'];

$criteria = '';

// back compaability
if(isset($_GET['statuskey']) && !empty($_GET['statuskey'])) $_GET['status_key'] = $_GET['statuskey'];   
if(isset($_GET['status_key']) && !empty($_GET['status_key']))
    $criteria .= ' and '. $obj->tableName.'.statuskey = ' . $obj->oDbCon->paramString($_GET['statuskey']);

switch ($module){ 
    case 'trucking-invoice':  
                        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php'; 
                        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderInvoice.class.php'; 
                        $fileName = 'truckingServiceOrderInvoice';
                        $obj = new TruckingServiceOrderInvoice();
                        $customer = new Customer();
                        
                        if(isset($_GET['customer_id']) && !empty($_GET['customer_id'])){
                            $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey'),' and ' . $customer->tableName.'.code = ' .$customer->oDbCon->paramString($_GET['customer_id']));  
                            $criteria .= ' and '. $obj->tableName.'.customerkey = ' . $obj->oDbCon->paramString($rsCustomer[0]['pkey']);
                        } 
         
                     break;
    case 'trucking-service-work-order':  
                        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php'; 
                        $fileName = 'truckingServiceWorkOrder';
                        $obj = new TruckingServiceWorkOrder();
          
          
                     break;
    case 'sales-order':  
                        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php'; 
                        $fileName = 'salesOrder';
                        $obj = new SalesOrder(); 
         
                     break;
    case 'purchase-order':  
                        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PurchaseOrder.class.php'; 
                        $fileName = 'purchaseOrder';
                        $obj = new PurchaseOrder(); 
         
                     break;
    case 'trucking-so-invoice':  
                        require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderInvoice.class.php'; 
                        $fileName = 'truckingServiceOrderInvoice';
                        $obj = new TruckingServiceOrderInvoice(); 
         
                     break;
}

$rsPrint = $obj->searchDataRow(array($obj->tableName.'.pkey'), ' and ' . $obj->tableName.'.code = ' .$obj->oDbCon->paramString($code) .$criteria);
if (empty($rsPrint)) endForDataNotFoundError();
$id = $rsPrint[0]['pkey']; 


$_GET['filename'] = $fileName;
$_GET['id'] = $id;


require_once $_SERVER ['DOCUMENT_ROOT'].'/admin/print/print.php';
?>