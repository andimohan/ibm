<?php

require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/PurchasePricing.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Item.class.php';

function getNewObj(){
    return new PurchasePricing();
}

$OBJ = getNewObj();
$supplier = new Supplier();
$item = new Item();

$purchasePriceDetail = array(
    'pkey' => array('paramName' => 'pkey'),
    'itemkey' => array('paramName' => 'item_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableItem . '.name'), 'ref' => array('obj' => $item, 'field' => 'name'), 'return' => array('paramName' => 'itemcode'),
    'itemname' => array('paramName' => 'item_name', 'updatable' => false, 'return' => array('paramName' => 'itemname'))),
    'price' => array('paramName' => 'price', 'mandatory' => true),
);

$API_FIELDS = array_merge($API_FIELDS, array(
    'code' => array('paramName' => 'code'),
    'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'supplierkey' => array('paramName' => 'supplier_id', 'mandatory' => true, 'ref' => array('obj' => $supplier, 'field' => 'code'), 'return' => array('paramName' => 'suppliercode')),
    'suppliername' => array('paramName' => 'supplier_name', 'updatable' => false, 'return' => array('paramName' => 'suppliername')),
    'notes' => array('paramName' => 'notes'),
    'statuskey' => array('paramName' => 'status_key', 'updatable' => false),
    'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail, 'tableName' => $OBJ->tableNameDetail, 'detail' => $purchasePriceDetail)
));

require_once '_process.php';

?>