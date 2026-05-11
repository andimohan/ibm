<?php
require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrder.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/TermOfPayment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Shipment.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/City.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Voucher.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/PaymentMethod.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/VoucherTransaction.class.php';   

$salesOrder = new SalesOrder();
//$warehouse = new Warehouse();
//$customer = new Customer();
//$termOfPayment = new TermOfPayment();
//$paymentMethod = new PaymentMethod();
//$shipment = new Shipment();
//$employee = new Employee();  
//$city = new City();
//$voucher = new Voucher();
//$item = new Item();
//$itemUnit = new ItemUnit();
//$voucherTransaction = new VoucherTransaction();

$url = API_URL.'sales-order';

// parameter ketiga untuk menentukan patokan PUT / POST
// kalo code (misalnya) sudah terdaftar, maka akan dianggap PUT
echo $salesOrder->executeImportAPI($url,$_POST['data'], 'code');

?>