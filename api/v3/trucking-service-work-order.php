<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceWorkOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingServiceOrderCategory.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Car.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Consignee.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Depot.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Terminal.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Location.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Supplier.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Chassis.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TruckingCostCashOut.class.php';

function getNewObj()
{
    return new TruckingServiceWorkOrder();
}

$OBJ = getNewObj();

$truckingServiceOrder = new TruckingServiceOrder();
$truckingServiceOrderCategory = new TruckingServiceOrderCategory();
$customer = new Customer();
$jobProgress = new JobProgress();
$car = new Car();
$employee = new Employee();
$consignee = new Consignee();
$truckingCost = new Service(TRUCKING_SERVICE,1);


$costDetail = array(
    'pkey' => array('paramName' => 'key'),
    'qty' => array('paramName' => 'qty'),
    'costkey' => array('paramName' => 'cost_id', 'mandatory' => true, 'ref' => array('obj' => new Service(), 'field' => 'code'), 'return' => array('paramName' => 'itemcode')),
    'name' => array('paramName' => 'cost_name', 'updatable' => false),
    'isneeddocument' => array('paramName' => 'is_need_document', 'updatable' => false),
    'reimburse' => array('paramName' => 'reimburse', 'updatable' => false),
    'fixedcost' => array('paramName' => 'fixed_cost', 'updatable' => false),
    'refcashoutkey' => array('paramName' => 'ref_cash_out_id', 'mandatory' => true, 'ref' => array('obj' => new TruckingCostCashOut(), 'field' => 'code'), 'return' => array('paramName' => 'refcashoutcode')),
    'requestamount' => array('paramName' => 'request_amount'),
    'amount' => array('paramName' => 'amount'),
    'employeekey' => array('paramName' => 'employee_id', 'mandatory' => true, 'ref' => array('obj' => new Employee(), 'field' => 'code'), 'return' => array('paramName' => 'employeecode')),
    'employeename' => array('paramName' => 'employee_name', 'updatable' => false),
    'supplierkey' => array('paramName' => 'supplier_id', 'mandatory' => true, 'ref' => array('obj' => new Supplier(), 'field' => 'code'), 'return' => array('paramName' => 'suppliercode')),
    'suppliername' => array('paramName' => 'supplier_name', 'updatable' => false),
    'total' => array('paramName' => 'total'),
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'stuffingdatetime' => array('paramName' => 'stuffingdate', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'stuffingaddress' => array('paramName' => 'stuffing_address'),
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => new Warehouse(), 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
    'refkey' => array('paramName' => 'sales_order_id'),
    'serviceorderdate'  => array('paramName' => 'salesorderdate', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'serviceordercode' => array('paramName' => 'sales_order_code', 'mandatory' => true, 'ref' => array('obj' => new TruckingServiceOrder, 'field' => 'code'), 'return' => array('paramName' => 'serviceordercode')),
    'refdetailkey' => array('paramName' => 'sales_order_detail_id'),
    'categorykey' => array('paramName' => 'sales_order_category_id', 'mandatory' => true, 'ref' => array('obj' => new TruckingServiceOrderCategory(), 'field' => 'code'), 'return' => array('paramName' => 'categorycode')),
    'categoryname' => array('paramName' => 'sales_order_category_name', 'mandatory' => true, 'return' => array('paramName' => 'categoryname')),
    'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'ref' => array('obj' => new Customer(), 'field' => 'code'), 'return' => array('paramName' => 'customercode')),
    'customername' => array('paramName' => 'customer_name', 'updatable' => false, 'return' => array('paramName' => 'customername')),
    'carkey' => array('paramName' => 'car_id', 'mandatory' => true, 'ref' => array('obj' => $car, 'field' => 'code'), 'return' => array('paramName' => 'carcode')),
    'policenumber' => array('paramName' => 'police_number', 'ref' => array('obj' => $car, 'field' => 'code'), 'return' => array('paramName' => 'policenumber')),
    'chassiskey' => array('paramName' => 'chassis_id', 'mandatory' => true, 'ref' => array('obj' => new Chassis(), 'field' => 'code'), 'return' => array('paramName' => 'chassiscode')),
    'chassisnumber' => array('paramName' => 'chassis_number', 'mandatory' => true, 'return' => array('paramName' => 'chassisnumber')),
    'jobtypename' => array('paramName' => 'job_type_name'),
    'containernumber' => array('paramName' => 'container_number'),
    'sealnumber' => array('paramName' => 'seal_number'),
    'container2number' => array('paramName' => 'container2_number'),
    'seal2number' => array('paramName' => 'seal2_number'),
    'itemkey' => array('paramName' => 'item_id', 'mandatory' => true, 'ref' => array('obj' => new Item(), 'field' => 'code'), 'return' => array('paramName' => 'containercode')),
    'containername' => array('paramName' => 'container_name'),
    'route' => array('paramName' => 'route'),
    'driverkey' => array('paramName' => 'driver_id', 'mandatory' => false, 'ref' => array('obj' => $employee, 'field' => 'code'), 'return' => array('paramName' => 'drivercode'),  'search' => array('field' => $employee->tableName . '.code')),
    'drivercommission' => array('paramName' => 'driver_commission'),
    'drivername' => array('paramName' => 'driver_name', 'updatable' => false, 'return' => array('paramName' => 'drivername')),
    'codriverkey' => array('paramName' => 'codriver_id', 'mandatory' => false, 'ref' => array('obj' => new Employee(), 'field' => 'code'), 'return' => array('paramName' => 'codrivercode')),
    'codrivername' => array('paramName' => 'codriver_name', 'updatable' => false, 'return' => array('paramName' => 'codrivername')),
    'codrivercommission' => array('paramName' => 'codriver_commission'),
    'consigneekey' => array('paramName' => 'consignee_id', 'mandatory' => false, 'ref' => array('obj' => $consignee, 'field' => 'code'), 'return' => array('paramName' => 'consigneecode'), 'search' => array('field' => $consignee->tableName . '.code')),
    'consigneename' => array('paramName' => 'consignee_name', 'updatable' => false, 'return' => array('paramName' => 'consigneename')),
    'depotkey' => array('paramName' => 'depot_id', 'mandatory' => true, 'ref' => array('obj' => new Depot(), 'field' => 'code'), 'return' => array('paramName' => 'depotcode')),
    'depotname' => array('paramName' => 'depot_name', 'updatable' => false, 'return' => array('paramName' => 'depotname')),
    'terminalkey' => array('paramName' => 'terminal_id', 'mandatory' => true, 'ref' => array('obj' => new Terminal(), 'field' => 'code'), 'return' => array('paramName' => 'terminalcode')),
    'terminalname' => array('paramName' => 'terminal_name', 'updatable' => false, 'return' => array('paramName' => 'terminalname')),
    'isoutsource' => array('paramName' => 'is_outsource'),
    'supplierkey' => array('paramName' => 'supplier_id', 'mandatory' => true, 'ref' => array('obj' => new Supplier(), 'field' => 'code'), 'return' => array('paramName' => 'outsourcecode')),
    'suppliername' => array('paramName' => 'supplier_name', 'updatable' => false, 'return' => array('paramName' => 'outsourcename')),
    'plannerkey' => array('paramName' => 'planner_id'),
    'locationkey' => array('paramName' => 'location_id', 'mandatory' => true, 'ref' => array('obj' => new Location(), 'field' => 'code'), 'return' => array('paramName' => 'locationcode')),
    'locationname' => array('paramName' => 'location_name', 'updatable' => false, 'return' => array('paramName' => 'locationname')),
    'outsourcecarregistrationnumber' => array('paramName' => 'outsource_car_registration_number'),
    'outsourcecost' => array('paramName' => 'outsource_cost'),
    'outsourcecostoutstanding' => array('paramName' => 'outsource_cost_outstanding'),
    'outsourcedownpayment' => array('paramName' => 'outsource_downpayment'),
    'outsourceap' => array('paramName' => 'outsource_ap'),
    'ispriceincludetax' => array('paramName' => 'is_price_include_tax'),
    'taxpercentage' => array('paramName' => 'tax_percentage'),
    'taxvalue' => array('paramName' => 'tax_value'),
    'cost_detail' => array('paramName' => 'cost_detail', 'dataset' => $OBJ->arrDataDetail, 'detail' => $costDetail),
));


require_once '_process.php';

?>