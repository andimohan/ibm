<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/AR.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ARPayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Currency.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PaymentMethod.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CostCashOut.class.php';

function getNewObj(){
    return new ARPayment();
}

$OBJ = getNewObj();
$warehouse = new Warehouse();
$customer = new Customer();
$paymentMethod = new PaymentMethod();
$employee = new Employee();  
$ar = new AR();
$currency = new Currency();
$costCashOut = new CostCashOut();
 
 
$costDetail = array(
    'pkey' => array('paramName' => 'key'),
    'amount' => array('paramName' => 'amount', 'mandatory' => true), 
    'costkey' => array('paramName' => 'cost_id', 'ref' => array('obj' => $costCashOut, 'field' => "code"),  'mandatory' => true, 'return' => array('paramName' => 'costcode')),  
    'costname' => array('paramName' => 'cost_name', 'updatable' => false,   'return' => array('paramName' => 'costname')) 
);


$paymentDetail = array(
    'pkey' => array('paramName' => 'key'),
    'amount' => array('paramName' => 'amount', 'mandatory' => true), 
    'paymentkey' => array('paramName' => 'payment_method_id', 'ref' => array('obj' => $paymentMethod, 'field' => "code"),  'mandatory' => true, 'return' => array('paramName' => 'paymentmethodcode')),  
    'paymentname' => array('paramName' => 'payment_method_name', 'updatable' => false,   'return' => array('paramName' => 'paymentmethodname')) 
);

$detail = array(
    'pkey' => array('paramName' => 'key'),
    'arkey' => array('paramName' => 'ar_id', 'mandatory' => true, 'ref' => array('obj' => $ar, 'field' => "code"), 'return' => array('paramName' => 'arcode')),  
	'amount' => array('paramName' => 'amount'), 
	'discount' => array('paramName' => 'discount'), 
	'outstanding' => array('paramName' => 'outstanding'),
	'taxamount' => array('paramName' => 'tax_pph_23'), 
);

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'),
    'trdate' => array('paramName' => 'date', 'mandatory' => true, 'return' => array('format' => 'mktime')),
    'warehousekey' => array('paramName' => 'warehouse_id', 'mandatory' => true, 'ref' => array('obj' => $warehouse, 'field' => 'code'), 'return' => array('paramName' => 'warehousecode') ),
    'warehousename' => array('paramName' => 'warehouse_name', 'updatable' => false, 'return' => array('paramName' => 'warehousename')),
    'customerkey' => array('paramName' => 'customer_id', 'mandatory' => true, 'search' => array('field' => $OBJ->tableCustomer.'.code'), 'ref' => array('obj' => $customer, 'field' => "code"), 'return' => array('paramName' => 'customercode')),
    'customername' => array('paramName' => 'customer_name',  'updatable' => false, 'return' => array('paramName' => 'customername')),
    'trnotes' => array('paramName' => 'description'),
    'currencykey' => array('paramName' => 'currency_id', 'ref' => array('obj' => $currency, 'field' => 'code'), 'return' => array('paramName' => 'currencycode') ),
	'rate' => array('paramName' => 'rate'),  
	
    'detail' => array('paramName' => 'detail', 'dataset' => $OBJ->arrDataDetail , 'tableName' => $OBJ->tableNameDetail, 'detail' =>  $detail),
	'payment_method_detail' => array('paramName' => 'payment_method_detail', 'dataset' => $OBJ->arrPaymentDetail, 'tableName' => $OBJ->tablePayment, 'detail' =>  $paymentDetail),
	'cost_detail' => array('paramName' => 'cost_detail', 'dataset' => $OBJ->arrCostDetail, 'tableName' => $OBJ->tableCost, 'detail' =>  $costDetail),
	
));

require_once '_process.php';

?>