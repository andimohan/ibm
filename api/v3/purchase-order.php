<?php
require_once '../../_config.php';
require_once '_include.php';

require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/PurchaseOrder.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Warehouse.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Supplier.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/TermOfPayment.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Shipment.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Employee.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/City.class.php'; 
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/Item.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/PaymentMethod.class.php';
require_once DOC_ROOT . 'include/' . CLASS_VERSION . '/ItemUnit.class.php'; 

function getNewObj()
{
   return new PurchaseOrder();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$supplier = new Supplier();
$termOfPayment = new TermOfPayment();
$paymentMethod = new PaymentMethod();
$shipment = new Shipment();
$employee = new Employee();
$city = new City(); 
$item = new Item();
$itemUnit = new ItemUnit(); 

// $imageUrl = array(
//    'pkey' => array('paramName' => 'key'),
//    'url' => array('paramName' => 'url'),
// );


$paymentDetail = array(
   'pkey' => array('paramName' => 'key'),
   'amount' => array('paramName' => 'amount', 'mandatory' => true),
   'paymentkey' => array('paramName' => 'payment_method_id', 'ref' => array('obj' => $paymentMethod, 'field' => "code"), 'mandatory' => true, 'return' => array('paramName' => 'paymentmethodcode')),
   'paymentname' => array('paramName' => 'payment_method_name', 'updatable' => false, 'return' => array('paramName' => 'paymentmethodname'))
);

$detail = array(
   'pkey' => array('paramName' => 'key'),
   'itemkey' => array('paramName' => 'item_id', 'mandatory' => true, 'ref' => array('obj' => $item, 'field' => "code"), 'return' => array('paramName' => 'itemcode')),
   'itemname' => array('paramName' => 'item_name', 'updatable' => false, 'return' => array('paramName' => 'itemname')),
   //'item_main_image_url' => array('paramName' => 'item_main_image_url', 'updatable' => false, 'return' => array('paramName' => 'itemmainimageurl')), 
   'qty' => array('paramName' => 'qty', 'mandatory' => true),
   'unitkey' => array('paramName' => 'unit_id', 'mandatory' => true, 'ref' => array('obj' => $itemUnit, 'field' => "code"), 'return' => array('paramName' => 'unitcode')),
   'unitname' => array('paramName' => 'unit_name', 'updatable' => false, 'return' => array('paramName' => 'unitname')), 
   'snlist' => array('paramName' => 'sn_list'),
   'detail_sn' =>  array('paramName' => 'detail_sn'),
   'priceinunit' => array('paramName' => 'price_in_unit'), // gk wajib, karena ad settingan nya priceMandatory
   'priceinpcs' =>  array('paramName' => 'price_in_pcs'),
   'discounttype' =>  array('paramName' => 'discount_type'),
   'discount' =>  array('paramName' => 'discount')
);

$API_FIELDS = array_merge($API_FIELDS, array(
   'code' => array('paramName' => 'code'),
   'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
   'refinvoicecode' => array('paramName' => 'reference'),
   'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode')),
   'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
   'supplierkey' => array('paramName' => 'supplier_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableSupplier . '.code'), 'ref' => array('obj' => $supplier, 'field' => "code"), 'return' => array('paramName' => 'suppliercode')),
   'suppliername' => array('paramName' => 'supplier_name', 'updatable' => false, 'return' => array('paramName' => 'suppliername')),

   'ispriceincludetax' => array('paramName' => 'price_include_tax'),
   'taxpercentage' => array('paramName' => 'tax_percentage'),

   'termofpaymentkey' => array('paramName' => 'term_of_payment_id', 'mandatory' => true, 'ref' => array('obj' => $termOfPayment, 'field' => "code"), 'return' => array('paramName' => 'termofpaymentcode')),
   'termofpayment' => array('paramName' => 'term_of_payment', 'updatable' => false, 'ref' => array('obj' => $termOfPayment), 'return' => array('paramName' => 'termofpaymentname')),
   'isfullreceive' => array('paramName' => 'full_receive'),
   'trdesc' => array('paramName' => 'description'),
   'statuskey'  =>  array('paramName' => 'status_key'), 
   'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail, 'tableName' => $OBJ->tableNameDetail, 'detail' => $detail),
   'payment_method_detail' => array('paramName' => 'payment_method_detail', 'dataset' => $OBJ->arrPaymentDetail, 'tableName' => $OBJ->tablePayment, 'detail' =>  $paymentDetail),
   
   
)
);

require_once '_process.php';

?>