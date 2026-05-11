<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/AP.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Currency.class.php';

function getNewObj()
{
   return new AP();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$supplier = new Supplier();
$currency = new Currency();

$arrAPType = array('pembelian barang' => 1, 'outsource jasa' => 2, 'komisi ritase' => 3, 'komisi penjualan' => 4, 'biaya maintenance (DN)' => 5, 'biaya lain' => 6, 'uang muka' => 7, 'nota debit (dn)' => 8);

$API_FIELDS = array_merge($API_FIELDS, array(
   'code' => array('paramName' => 'code'),
   'refcode' => array('paramName' => 'ref_code'), 
   'aptype' => array('paramName' => 'transaction_type', 'mandatory' => true, 'ref' => array('dataset' => $arrAPType), 'return' => array('paramName' => 'transactiontype')),
   //'artype' => array('paramName' => 'transaction_type', 'mandatory' => true, 'search' => array('field' => $OBJ->tableType.'.name'), 'ref' => array('obj' => $OBJ->tableType, 'field' => "name"), 'return' => array('paramName' => 'name')),
   'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
   'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
   'supplierkey' => array('paramName' => 'supplier_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableSupplier . '.code'), 'ref' => array('obj' => $supplier, 'field' => "code"), 'return' => array('paramName' => 'suppliercode')),
   'suppliername' => array('paramName' => 'supplier_name', 'updatable' => false, 'return' => array('paramName' => 'suppliername')),
   'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
   'duedate' => array('paramName' => 'due_date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
   'currencykey' => array('paramName' => 'currency_id', 'ref' => array('obj' => $currency, 'field' => 'code'), 'return' => array('paramName' => 'currencycode')),
   'rate' => array('paramName' => 'rate'),
   'amount' => array('paramName' => 'amount'),
   'trdesc' => array('paramName' => 'notes'),
   'statuskey' => array('paramName' => 'status_key'),
   'overwriteGL' => array('paramName' => 'overwrite_gl'),
)
);


require_once '_process.php';
?>