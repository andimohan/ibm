<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/AR.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Customer.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Currency.class.php';

function getNewObj()
{
   return new AR();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$customer = new Customer();
$currency = new Currency();

$arrARType = array('penjualan barang' => '1', 'penjualan jasa' => '2', 'nota kredit (cn)' => '3', 'uang muka' => '4');

$API_FIELDS = array_merge($API_FIELDS, array(
   'code' => array('paramName' => 'code'),
   'refcode' => array('paramName' => 'ref_code'), 
   'artype' => array('paramName' => 'transaction_type', 'mandatory' => true, 'ref' => array('dataset' => $arrARType), 'return' => array('paramName' => 'transactiontype')),
   //'artype' => array('paramName' => 'transaction_type', 'mandatory' => true, 'search' => array('field' => $OBJ->tableType.'.name'), 'ref' => array('obj' => $OBJ->tableType, 'field' => "name"), 'return' => array('paramName' => 'name')),
   'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
   'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
   'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCustomer . '.code'), 'ref' => array('obj' => $customer, 'field' => "code"), 'return' => array('paramName' => 'customercode')),
   'customername' => array('paramName' => 'customer_name', 'updatable' => false, 'return' => array('paramName' => 'customername')),
   'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
   'duedate' => array('paramName' => 'due_date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
   'currencykey' => array('paramName' => 'currency_id', 'ref' => array('obj' => $currency, 'field' => 'code'), 'return' => array('paramName' => 'currencycode')),
   'rate' => array('paramName' => 'rate'),
   'amount' => array('paramName' => 'amount'),
   'trdesc' => array('paramName' => 'notes'),
   'statuskey' => array('paramName' => 'status_key'),
   'overwriteGL' => array('paramName' => 'overwrite_gl'),
));


require_once '_process.php';
?>