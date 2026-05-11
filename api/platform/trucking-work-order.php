<?php 
require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php';     
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';

function getNewObj(){ return  new TruckingServiceWorkOrder(); } 
$OBJ = getNewObj(); 
$truckingServiceOrder = new TruckingServiceOrder();

$costDetail = array( 
    'pkey' => array('paramName' => 'key'),   
    'employeekey' =>  array('paramName' => 'employee_name',  'ref' => array('obj' => new Employee() )),  
    'supplierkey' =>  array('paramName' => 'supplier_name', 'ref' => array('obj' => new Supplier() )),  
    'qty'  =>  array('paramName' => 'qty','mandatory' => true ), 
    'taxpercentage'  =>  array('paramName' => 'tax_percentage' ), 
    'tax23percentage'  =>  array('paramName' => 'tax23_percentage' ),  
    'costkey' =>  array('paramName' => 'cost_name', 'mandatory' => true, 'ref' => array('obj' => new Service(TRUCKING_SERVICE,1) )),  
    'requestamount'  =>  array('paramName' => 'request_amount','mandatory' => true ) 
);

$vehicleDetail = array( 
    'pkey' => array('paramName' => 'key'),   
    'itemkey' =>  array('paramName' => 'service_id', 'ref' => array('obj' => new Service(), 'field' => 'code' ), 'return' => array('paramName' => 'itemcode')),  // ambil otomatis dr JO aj kalo gk dikirim
    'itemname' =>  array('paramName' => 'service_name','updatable' => false),  // ambil otomatis dr JO aj kalo gk dikirim
    'carregistrationnumber' =>  array('paramName' => 'vehicle_registration_number'),
    'container' =>  array('paramName' => 'container_number'),
    'seal' =>  array('paramName' => 'seal_number'), 
    'qty' =>  array('paramName' => 'qty', 'mandatory' => true), 
    'price'  =>  array('paramName' => 'price','mandatory' => true ), 
    'taxpercentage' =>  array('paramName' => 'tax_percentage'),  
    'tax23percentage' =>  array('paramName' => 'tax23_percentage'),  
);
  
     
$API_FIELDS = array_merge(array(   
                'trdate'  =>  array('paramName' => 'date','mandatory' => true, 'return' => array('format' => 'mktime')  ), 
                'stuffingdatetime'  =>  array('paramName' => 'stuffing_date','mandatory' => true, 'return' => array('format' => 'mktime')  ), 
                'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' ), 'return' => array('paramName' => 'warehousecode')), 
                'refkey'  =>  array('paramName' => 'job_order_id','mandatory' => true ,  'ref' => array('obj' => $truckingServiceOrder, 'field' => 'code' ), 'return' => array('paramName' => 'serviceordercode'), 'search' => array('field' => $truckingServiceOrder->tableName.'.code') ),
                'refdetailkey'  =>  array('paramName' => 'jo_detail_id', 'return' => array('isReturn' => false)),  
                'isoutsource'  =>  array('paramName' => 'is_outsource'), 
                'supplierkey'  =>  array('paramName' => 'supplier_id', 'ref' => array('obj' =>  new Supplier(), 'field' => 'code'), 'return' => array('paramName' => 'outsourcesuppliercode'), 'search' => array('field' => 'outsource_supplier.code') ),
                'outsourcesuppliername'  =>  array('paramName' => 'supplier_name'), 
                'trdesc'  => array('paramName' => 'description'),
                'productdesc' => array('paramName' => 'goods_description'),
                'auto_proceed' => array('paramName' => 'auto_proceed'),  
                'vehicle_detail' =>  array('paramName' => 'vehicle_detail','dataset' => $OBJ->arrCarDetail, 'tableName' => $OBJ->tableWorkOrderCarDetail, 'detail' =>  $vehicleDetail),
                'cost_detail' =>  array('paramName' => 'cost_detail',  'dataset' => $OBJ->arrDataDetail, 'tableName' => $OBJ->tableCost, 'detail' =>  $costDetail, 'return' => array('isReturn' => false)),   
               ),$API_FIELDS); 
  
$RETURN_FIELDS =   array( 
                    'statuscolor' =>  array('paramName' => 'status_color'), 
                );
               
                
require_once '_process.php';
     
?>