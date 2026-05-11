<?php

require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/CarServiceMaintenance.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Employee.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Item.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ItemUnit.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Car.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php'; 



function getNewObj()
{
    return new CarServiceMaintenance();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$employee = new Employee();
$item = new Item();
$itemUnit = new ItemUnit();
$car = new Car();
$supplier = new Supplier();

$arrTempCategory = $OBJ->getMaintenanceCategory();
$arrTempCategory = array_column($arrTempCategory, 'pkey', 'name');
$arrCategoryMaintenance = array();
foreach ($arrTempCategory as $key => $row) {
    $arrCategoryMaintenance[strtolower($key)] = $row;
}

$arrTempType = $OBJ->getMaintenanceType();
$arrTempType = array_column($arrTempType, 'pkey', 'name');
$arrTypeMaintenance = array();
foreach ($arrTempType as $key => $row) {
    $arrTypeMaintenance[strtolower($key)] = $row;
}

$maintenanceDetail = array(
    'pkey' => array('paramName' => 'pkey'),
    'itemkey' => array('paramName' => 'item_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableItem . '.name'), 'ref' => array('obj' => $item, 'field' => 'name'), 'return' => array('paramName' => 'itemcode')),
    'itemcode' => array('paramName' => 'item_code', 'updatable' => false, 'return' => array('paramName' => 'itemcode')),
    'trdesc' => array('paramName' => 'description'),
    'qty' => array('paramName' => 'qty', 'mandatory' => true),
    'unitkey' => array('paramName' => 'item_unit_id', 'mandatory' => true, 'ref' => array('obj' => $itemUnit, 'field' => 'name'), 'return' => array('paramName' => 'itemunitname')),
    'unitname' => array('paramName' => 'item_unit_name', 'updatable' => false, 'return' => array('paramName' => 'itemunitname')),
    'priceinunit'  => array('paramName' => 'price')
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'trdate' => array('paramName' => 'date', 'mandatory' => true),
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
    'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
    'categorykey' => array('paramName' => 'category_id', 'mandatory' => true , 'ref' => array('dataset' => $arrCategoryMaintenance), 'return' => array('paramName' => 'categoryname')),  
    'techniciankey' => array('paramName' => 'technician_id', 'ref' => array('obj' => $employee, 'field' => 'code'), 'return' => array('paramName' => 'techniciancode')),
    'technicianname' => array('paramName' => 'technician_name', 'updatable' => false, 'return' => array('paramName' => 'techniciancode')),
    'isoutsource' => array('paramName' => 'is_outsource'),
    'supplierkey' => array('paramName' => 'supplier_id', 'ref' => array('obj' => $supplier, 'field' => 'code'), 'return' => array('paramName' => 'suppliercode')),
    'suppliername' => array('paramName' => 'supplier_name', 'updatable' => false, 'return' => array('paramName' => 'suppliername')),
    'refcode' => array('paramName' => 'invoice_reference'),
    'typekey' => array('paramName' => 'type_id', 'mandatory' => true, 'ref' => array('dataset' => $arrTypeMaintenance), 'return' => array('paramName' => 'type_id')),
    'typename' => array('paramName' => 'type_name', 'updatable' => false, 'return' => array('paramName' => 'typename')),
    'carkey' => array('paramName' => 'car_id', 'mandatory' => true, 'ref' => array('obj' => $car, 'field' => 'policenumber'), 'return' => array('paramName' => 'warehousecode')),
    'policenumber' => array('paramName' => 'police_number', 'updatable' => false, 'return' => array('paramName' => 'policenumber')),
    'mileage' => array('paramName' => 'mile_age'),
    'driverkey' => array('paramName' => 'driver_id', 'ref' => array('obj' => $employee, 'field' => 'code'), 'return' => array('paramName' => 'drivercode')),
    'drivername' => array('paramName' => 'driver_name', 'updatable' => false, 'return' => array('paramName' => 'drivername')),
    'trnotes' => array('paramName' => 'note'),
    'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail, 'tableName' => $OBJ->tableNameDetail, 'detail' => $maintenanceDetail)
));

require_once '_process.php';

?>