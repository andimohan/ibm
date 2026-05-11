<?php
require_once '../../_config.php';  
require_once '_include.php';
require_once '_global.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderInvoice.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php'; 

$obj = null;

$code = $_GET['code'];
$module = $_GET['module'];

$criteria = '';

switch ($module){ 
    case 'trucking-invoice':  
                        $fileName = 'truckingServiceOrderInvoice';
                        $obj = new TruckingServiceOrderInvoice();
                        $customer = new Customer();
                        
                        if(isset($_GET['customer_id']) && !empty($_GET['customer_id'])){
                            $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey'),' and ' . $customer->tableName.'.code = ' .$customer->oDbCon->paramString($_GET['customer_id']));  
                            $criteria .= ' and '. $obj->tableName.'.customerkey = ' . $obj->oDbCon->paramString($rsCustomer[0]['pkey']);
                        }
                                
                        // back compaability
                        if(isset($_GET['statuskey']) && !empty($_GET['statuskey'])) $_GET['status_key'] = $_GET['statuskey'];   
                        if(isset($_GET['status_key']) && !empty($_GET['status_key']))
                            $criteria .= ' and '. $obj->tableName.'.statuskey = ' . $obj->oDbCon->paramString($_GET['statuskey']);
                       
        
                        $rsInvoice = $obj->searchDataRow(array($obj->tableName.'.pkey'), ' and ' . $obj->tableName.'.code = ' .$obj->oDbCon->paramString($code) .$criteria);
        
        
                        if (empty($rsInvoice)) endForDataNotFoundError();
                        $id = $rsInvoice[0]['pkey']; 
                     break;
}

$_GET['filename'] = $fileName;
$_GET['id'] = $id;

require_once $_SERVER ['DOCUMENT_ROOT'].'/admin/print/print.php';
?>