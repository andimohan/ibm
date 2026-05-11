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
 
$OBJ = new TruckingServiceOrder(); 
$location = new Location();
$employee = new Employee();
$truckingCost =  new Service(TRUCKING_SERVICE,1);   

$arrTemp = $OBJ->getCargoType();
$arrTemp = array_column($arrTemp, 'pkey','name');  
$arrCargoType = array();
foreach($arrTemp as $key=>$row)
 $arrCargoType[strtolower($key)] = $row;
          

$sellingCost = array( 
    'pkey' => array('paramName' => 'pkey'),
    'costkey' =>  array('paramName' => 'cost_name', 'mandatory' => true, 'ref' => array('obj' => $truckingCost )),  
    'qty'  =>  array('paramName' => 'qty','mandatory' => true ), 
    'price'  =>  array('paramName' => 'selling_price','mandatory' => true ) 
);
  
$headerCost = array( 
    'pkey' => array('paramName' => 'pkey'),
    'costkey' =>  array('paramName' => 'cost_name', 'mandatory' => true, 'ref' => array('obj' => $truckingCost )),  
    'qty'  =>  array('paramName' => 'qty','mandatory' => true ), 
    'requestamount'  =>  array('paramName' => 'request_amount','mandatory' => true ) 
);
  

$serviceDetail = array( 
    'pkey' => array('paramName' => 'pkey'),
    'itemkey' =>  array('paramName' => 'service_name', 'mandatory' => true, 'ref' => array('obj' =>  new Service() )), 
    'qtyinbaseunit' =>  array('paramName' => 'qty', 'mandatory' => true),
    'trdate'  =>  array('paramName' => 'shipment_date','mandatory' => true ), 
    'priceinunit'  =>  array('paramName' => 'price','mandatory' => true ), 
    'trdesc'  =>  array('paramName' => 'work_order_note' ), 
    'isgroup'  =>  array('paramName' => 'group' ) 
);
  
     
$API_FIELDS = array_merge($API_FIELDS,array(
                'code' =>   array('paramName' => 'code'),  
                'trdate'  =>  array('paramName' => 'date','mandatory' => true ), 
                'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' )), 
                'customerkey'  =>  array('paramName' => 'customer_id','mandatory' => true, 'ref' => array('obj' => new Customer(), 'field' => 'code' )),  
                'contractkey'  =>  array('paramName' => 'contract_id', 'ref' => array('obj' => new TruckingSellingRate(), 'field' => 'code' )),  
                'categorykey'  =>  array('paramName' => 'category_name','mandatory' => true,  'ref' => array('obj' => new TruckingServiceOrderCategory())),  
                'cargotypekey'  =>  array('paramName' => 'cargo_type','mandatory' => true , 'ref' => array('dataset' => $arrCargoType)),  
                'donumber'  =>  array('paramName' => 'shipping_instruction'), 
                'shipmentnumber'  =>  array('paramName' => 'shipment_number'), 
                'terminalkey'  =>  array('paramName' => 'terminal_name',  'ref' => array('obj' => new Terminal())),  
                'depotkey'  =>  array('paramName' => 'depot_name',  'ref' => array('obj' => new Depot())),  
                'consigneekey'  =>  array('paramName' => 'consignee_id',  'ref' => array('obj' => new Consignee(), 'field' => 'code' )),   
                'stuffinglocationkey'  =>  array('paramName' => 'stuffing_location_name', 'ref' => array('obj' => $location)), 
                'stuffingaddress'  =>   array('paramName' => 'stuffing_address'),   
                'trdesc'  =>  array('paramName' => 'description'),     
                'plannerkey'  =>  array('paramName' => 'planner_id',  'ref' => array('obj' => $employee)),  
                'routefrom'  =>  array('paramName' => 'route_from'),     
                'routeto'  =>  array('paramName' => 'route_to'),   
                'saleskey'  =>  array('paramName' => 'sales_id',  'ref' => array('obj' => $employee)),     
                'service_detail' =>  array('paramName' => 'service_detail', 'mandatory' => true, 'isDetail' => true, 'dataset' => $OBJ->arrDataDetail, 'detail' =>  $serviceDetail),
                'additional_cost_detail' =>  array('paramName' => 'additional_cost',  'dataset' => $OBJ->arrHeaderCost, 'detail' =>  $headerCost),
                'additional_selling_detail' =>  array('paramName' => 'additional_selling',  'dataset' => $OBJ->arrSellingCost, 'detail' =>  $sellingCost)
            )); 
  
$RETURN_FIELDS =   array(
                    'customername' =>   array('paramName' => 'customer_name'),  
                    'consigneename' =>   array('paramName' => 'consignee_name'),  
                    'statuskey' =>   array('paramName' => 'status_key'),  
                    'statuscolor' =>  array('paramName' => 'status_color'), 
                );
               
                
require_once '_process.php';
     
?>
