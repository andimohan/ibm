<?php
require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Category.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Terminal.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Location.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingSellingRate.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderCategory.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Depot.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Consignee.class.php';
 
function getNewObj(){ return new TruckingServiceOrder(); }
$OBJ = getNewObj();

$location = new Location();
$employee = new Employee();
$customer = new Customer();
$truckingCost =  new Service(TRUCKING_SERVICE,1);   

$arrTemp = $OBJ->getCargoType();
$arrTemp = array_column($arrTemp, 'pkey','name');  
$arrCargoType = array();
foreach($arrTemp as $key=>$row)
 $arrCargoType[strtolower($key)] = $row;
             
$consigneeDefaultValue = array(
    'code' => 'xxxxxx',
    'selStatus' => 1,
    'hidLocationKey' => 1,
);

$sellingCost = array( 
    'pkey' => array('paramName' => 'key'),   
    'costkey' =>  array('paramName' => 'cost_id', 'mandatory' => true, 'ref' => array('obj' => $truckingCost, 'field' => 'code' ), 'return' => array('paramName' => 'itemcode')), 
    'itemname' =>  array('paramName' => 'cost_name','updatable' => false), 
    'qty'  =>  array('paramName' => 'qty','mandatory' => true ), 
    'price'  =>  array('paramName' => 'price','mandatory' => true ),
    'requestid'  =>  array('paramName' => 'request_id' ) 
);
  
$headerCost = array( 
    'pkey' => array('paramName' => 'key'),  
    'costkey' =>  array('paramName' => 'cost_id', 'mandatory' => true, 'ref' => array('obj' => $truckingCost, 'field' => 'code' ), 'return' => array('paramName' => 'itemcode')), 
    'itemname' =>  array('paramName' => 'cost_name','updatable' => false), 
    'employeekey' =>  array('paramName' => 'recipient_id', 'ref' => array('obj' => $employee, 'field' => 'code' ), 'return' => array('paramName' => 'recipientcode')), 
    'recipientname' =>  array('paramName' => 'recipient_name', 'updatable' => false), 
    'qty'  =>  array('paramName' => 'qty','mandatory' => true ), 
    'requestamount'  =>  array('paramName' => 'request_amount','mandatory' => true ), 
    'amount'  =>  array('paramName' => 'amount', 'updatable' => false), 
    'requestid'  =>  array('paramName' => 'request_id' ) ,
);
  
$serviceDetail = array( 
    'pkey' => array('paramName' => 'key'),
    'itemkey' => array('paramName' => 'service_id', 'mandatory' => true, 'ref' => array('obj' => new Service(), 'field' => 'code' ), 'return' => array('paramName' => 'itemcode')), 
    'itemname' => array('paramName' => 'service_name','updatable' => false),
    'qtyinbaseunit' =>  array('paramName' => 'qty', 'mandatory' => true),
    'trdate'  =>  array('paramName' => 'shipment_date','mandatory' => true, 'return' => array('format' => 'mktime')), 
    'priceinunit'  =>  array('paramName' => 'price','mandatory' => true ), 
    'trdesc'  =>  array('paramName' => 'work_order_note' ), 
    'isgroup'  =>  array('paramName' => 'group' ) , 
    'requestid'  =>  array('paramName' => 'request_id' ) 
);
  
$invoiceDetail = array( 
    'pkey' => array('paramName' => 'key'),  
    'requestid' => array('paramName' => 'request_id'),  
    'invoicetypekey' => array('paramName' => 'invoice_type_key'),  
    'invoicetypename' => array('paramName' => 'invoice_type_name'),  
    'code' =>  array('paramName' => 'invoice_id', 'updatable' => false), 
    'trdate' =>  array('paramName' => 'invoice_date', 'updatable' => false,  'return' => array('format' => 'mktime')),  
    'amount'  =>  array('paramName' => 'amount','updatable' => false ),  
    'statuskey'  =>  array('paramName' => 'status_key','updatable' => false ),  
    'statusname'  =>  array('paramName' => 'status_name','updatable' => false ),  
    'grandtotal'  =>  array('paramName' => 'invoice_amount','updatable' => false)
);
   
$containerDetail = array( 
    'pkey' => array('paramName' => 'key'),   
    'requestid' => array('paramName' => 'request_id'),  
    'container' =>  array('paramName' => 'container_number', 'mandatory' => true), 
    'seal' =>  array('paramName' => 'seal_number')
);
  
    
/*$invoiceProformaDetail = array( 
    'pkey' => array('paramName' => 'key'),  
    'requestid' => array('paramName' => 'request_id'),  
    'invoicetypekey' => array('paramName' => 'invoice_type_key'),  
    'invoicetypename' => array('paramName' => 'invoice_type_name'),  
    'code' =>  array('paramName' => 'invoice_id', 'updatable' => false), 
    'trdate' =>  array('paramName' => 'invoice_date', 'updatable' => false,  'return' => array('format' => 'mktime')),  
    'amount'  =>  array('paramName' => 'amount','updatable' => false ),
    'grandtotal'  =>  array('paramName' => 'invoice_amount','updatable' => false)
);*/
    
$API_FIELDS = array_merge(array(
                'requestid'  =>  array('paramName' => 'request_id' ) ,
                'trdate'  =>  array('paramName' => 'date','mandatory' => true, 'return' => array('format' => 'mktime') ), 
                'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' ), 'return' => array('paramName' => 'warehousecode')), 
                'customerkey'  =>  array('paramName' => 'customer_id','mandatory' => true, 'ref' => array('obj' => $customer, 'field' => 'code' ) , 'return' => array('paramName' => 'customercode'), 'search' => array('field' => $customer->tableName.'.code')),  
                
                // utk return dan utk fitur kedepan kalo bisa terima nama customer jg
                'customername'  =>  array('paramName' => 'customer_name','updatable' => false, 'return' => array('paramName' => 'customername')),
                'contractkey'  =>  array('paramName' => 'contract_id', 'ref' => array('obj' => new TruckingSellingRate(), 'field' => 'code' ), 'return' => array('isReturn' => false)),  
                'categorykey'  =>  array('paramName' => 'category_name','mandatory' => true,  'ref' => array('obj' => new TruckingServiceOrderCategory()), 'return' => array('paramName' => 'categoryname')),  
                'cargotypekey'  =>  array('paramName' => 'cargo_type','mandatory' => true , 'ref' => array('dataset' => $arrCargoType), 'return' => array('paramName' => 'cargotype')),  
                'donumber'  =>  array('paramName' => 'shipping_instruction'), 
                'shipmentnumber'  =>  array('paramName' => 'shipment_number'), 
                'poreference'  =>  array('paramName' => 'po_reference'), 
                'terminalkey'  =>  array('paramName' => 'terminal_id',  'ref' => array('obj' => new Terminal(), 'field' => 'code'), 'return' => array('paramName' => 'terminalcode')),  
                'terminalname'  =>  array('paramName' => 'terminal_name','updatable' => false),  
                'depotkey'  =>  array('paramName' => 'depot_id',  'ref' => array('obj' => new Depot(), 'field' => 'code'), 'return' => array('paramName' => 'depotcode')),  
                'depotname'  =>  array('paramName' => 'depot_name','updatable' => false),  
                //'consigneekey'  =>  array('paramName' => 'consignee_id',  'ref' => array('obj' => new Consignee(), 'field' => 'code' ), 'return' => array('paramName' => 'consigneecode')),   
                'consigneekey'  =>  array('paramName' => 'consignee_name', 'ref' => array('obj' => new Consignee(), 'autoAdd' => true, 'defaultValue' => $consigneeDefaultValue), 'return' => array('paramName' => 'consigneename')),  
                'stuffinglocationkey'  =>  array('paramName' => 'stuffing_location_name', 'ref' => array('obj' => $location), 'return' => array('paramName' => 'locationname')),  
                'stuffingaddress'  =>   array('paramName' => 'stuffing_address'),   
                'trdesc'  =>  array('paramName' => 'description'),     
                'plannerkey'  =>  array('paramName' => 'planner_id',  'ref' => array('obj' => $employee), 'return' => array('isReturn' => false)),  
                'routefrom'  =>  array('paramName' => 'route_from'),     
                'routeto'  =>  array('paramName' => 'route_to'),   
                'saleskey'  =>  array('paramName' => 'sales_id',  'ref' => array('obj' => $employee), 'return' => array('paramName' => 'salescode')),     
                'salesname'  =>  array('paramName' => 'sales_name','updatable' => false),   
                'changestatusto' => array('paramName' => 'change_status_to', 'forceParam' => true, 'return' => array('isReturn' => false)),
                //'autoinvoice'  =>  array('paramName' => 'auto_invoice') ,       
                'service_detail' =>  array('paramName' => 'service_detail', 'dataset' => $OBJ->arrDataDetail ,'detail' =>  $serviceDetail),
                'additional_cost_detail' =>  array('paramName' => 'additional_cost_detail',  'dataset' => $OBJ->arrHeaderCost,  'tableName' => $OBJ->tableHeaderCost , 'detail' =>  $headerCost),
                'additional_selling_detail' =>  array('paramName' => 'additional_selling_detail',  'dataset' => $OBJ->arrSellingCost, 'tableName' => $OBJ->tableSellingCost , 'detail' =>  $sellingCost),
                'container_detail' =>  array('paramName' => 'container_detail',  'dataset' => $OBJ->arrContainerDetail, 'tableName' => $OBJ->tableContainerDetail , 'detail' =>  $containerDetail),
                'invoice_detail' =>  array('paramName' => 'invoice_detail', 'updatable' => false, 'detail' =>  $invoiceDetail),
               // 'invoice_proforma_detail' => array('paramName' => 'invoice_proforma_detail', 'updatable' => false, 'detail' =>  $invoiceProformaDetail),
            ),$API_FIELDS); 
   
// tambahan
$RETURN_FIELDS = array(       
                'statuscolor' =>  array('paramName' => 'status_color'),  
            );


require_once '_process.php';

?>
