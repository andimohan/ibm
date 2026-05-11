<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemAdjustment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Company.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';

function getNewObj(){
    return new ItemAdjustment();
}

$OBJ = getNewObj();

$warehouse = new Warehouse();
$company = new Company();
$item = new Item();
$itemUnit = new ItemUnit();

$detail = array(
    'pkey' => array('paramName' => 'key'),
    'itemkey' => array('paramName' => 'item_id','skipOnNotFound'=>true, 'mandatory' => true, 'ref' => array('obj' => $item, 'field' => "code"), 'return' => array('paramName' => 'itemcode')), 
    'itemname' =>  array('paramName' => 'item_name', 'updatable' => false, 'return' => array('paramName' => 'itemname')),
//    'qty' =>  array('paramName' => 'qty', 'mandatory' => true),
    'qtybefore' =>  array('paramName' => 'qty_before'),
    'qtyafter' =>  array('paramName' => 'qty_after'),
//    'unitkey' => array('paramName' => 'unit_id', 'mandatory' => true, 'ref' => array('obj' => $itemUnit, 'field' => "code"), 'return' => array('paramName' => 'unitcode')),
    'unitname' => array('paramName' => 'unit_name', 'updatable' => false, 'return' => array('paramName' => 'unitname')),
    'unitconvmultiplier' => array('paramName' => 'unit_conv_multiplier'),
    'qtyinbaseunit' =>  array('paramName' => 'qty_in_base_unit'),
    'costinbaseunit' =>  array('paramName' => 'cost_in_base_unit')
);

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'),   
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode') ),
    'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
    'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'trdesc' => array('paramName' => 'description'),
    //'isfullreceive' => array('paramName' => 'is_full_receive'),
    'statuskey'  =>  array('paramName' => 'status_key', 'updatable' => false), 
    'items_detail' => array('paramName' => 'items_detail', 'dataset' => $OBJ->arrDataDetail , 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $detail)
));

require_once '_process.php';

?>