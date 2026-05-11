<?php

require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/EMKLJobOrder.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Container.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Currency.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Port.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Vessel.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Consignee.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php';    
 
$OBJ = new EMKLJobOrder(EMKL['jobType']['export']);
$container = new Container();
$service  = new Service(SERVICE);
$currency = new Currency();
$customer = new Customer();
$vessel = new Vessel(); 
$port = new Port();
 
$arrTemp = $OBJ->getTransportationType(); 
$arrTemp = array_column($arrTemp, 'pkey','name');  
$arrFreightType = array();
foreach($arrTemp as $key=>$row)
 $arrFreightType[strtolower($key)] = $row;
 
$arrTemp = $OBJ->getLoadContainer();
$arrTemp = array_column($arrTemp, 'pkey','name');  
$arrLoadType = array();
foreach($arrTemp as $key=>$row)
 $arrLoadType[strtolower($key)] = $row;
 

// INPUT QUERY
// field yang diterima dari parameter API
// convert ke nama parameter kita di class 
    
$itemsDetail = array( 
    'pkey' => array('paramName' => 'pkey'),
    'itemkey' => array('paramName' => 'container_name', 'ref' => array('obj' => $container)), 
    'servicekey' => array('paramName' => 'service_name', 'mandatory' => true, 'ref' => array('obj' => $service)), 
    'currencykey' =>  array('paramName' => 'currency', 'mandatory' => true, 'ref' => array('obj' => $currency)), 
    'qty' =>  array('paramName' => 'qty' ,'mandatory' => true),
    'priceinunit' =>  array('paramName' => 'price' ,'mandatory' => true),
);

$salesDetail = array( 
    'pkey' => array('paramName' => 'pkey'),
    'customerkey' => array('paramName' => 'customer_id', 'ref' => array('obj' => $customer, 'field' => 'code' )),  
    'hbl' => array('paramName' => 'hbl'),  
    'currencykey' =>  array('paramName' => 'currency', 'mandatory' => true, 'ref' => array('obj' => $currency)), 
    'rate' =>  array('paramName' => 'rate', 'mandatory' => true), 
    'items_detail' =>  array('paramName' => 'items_detail', 'mandatory' => true, 'dataset' => $OBJ->arrItem, 'detail' =>  $itemsDetail)
);
    
$API_FIELDS = array_merge($API_FIELDS,array(
                'code' =>   array('paramName' => 'code'),  
                'trdate'  =>  array('paramName' => 'date','mandatory' => true ), 
                'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' )), 
                'carrierkey'  =>  array('paramName' => 'carrier_id', 'ref' => array('obj' => new Supplier(), 'field' => 'code' )),  
                'employeekey'  =>  array('paramName' => 'sales_id', 'ref' => array('obj' => new Employee(), 'field' => 'code' )),  
                'transportationtypekey'  =>  array('paramName' => 'freight_type' , 'ref' => array('dataset' => $arrFreightType)),  
                'loadcontainertypekey'  =>  array('paramName' => 'load_type', 'ref' => array('dataset' => $arrLoadType)),  
                'polkey'  =>  array('paramName' => 'pol_name', 'ref' => array('obj' => $port)), 
                'podkey'  =>  array('paramName' => 'pod_name', 'ref' => array('obj' => $port)), 
                'bookingnumber'  =>  array('paramName' => 'booking_number'), 
                'mblnumber'  =>  array('paramName' => 'mbl'), 
                'etdpol'  =>  array('paramName' => 'etd', 'mandatory' => true ), 
                'etapod'  =>  array('paramName' => 'eta', 'mandatory' => true ), 
                'aju'  =>  array('paramName' => 'aju'  ), 
                'peb'  =>  array('paramName' => 'peb'  ),  
                'ponumber'  =>  array('paramName' => 'po_reference'),  
                'vesselkey'  =>  array('paramName' => 'vessel_id', 'ref' => array('obj' => new Vessel(), 'field' => 'code' )), 
                'vesselnumber'  =>  array('paramName' => 'vessel_number'), 
                'containernumber'  =>  array('paramName' => 'container_number'),  
                'trdesc'  =>  array('paramName' => 'description'),   
                'sales_detail' =>  array('paramName' => 'sales_detail', 'mandatory' => true, 'dataset' => $OBJ->arrDataDetail, 'detail' =>  $salesDetail)
            ));
    
require_once '_process.php';
     
?>