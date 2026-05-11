<?php

require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashBankIn.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/RevenueCashIn.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChartOfAccount.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';  
 
$OBJ = new CashBankIn();

$bankDetail = array( 
    'pkey' => array('paramName' => 'pkey'),
    'customerkey' => array('paramName' => 'customer_name',  'ref' => array('obj' =>  new Customer() )), 
	'revenuekey' => array('paramName' => 'revenue_name', 'ref' => array('obj' =>  new RevenueCashIn())), 
    'amount' =>  array('paramName' => 'amount' ,'mandatory' => true),
    'trdesc' => array('paramName' => 'description')
);

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'), 
    'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' )), 
    'coakey'  =>  array('paramName' => 'coa_id','mandatory' => true, 'ref' => array('obj' => new ChartOfAccount(),  'field' => 'code')),
    'trdate'  =>  array('paramName' => 'date','mandatory' => true ), 
    'trdesc'  =>  array('paramName' => 'note'), 
    'cash_detail' =>  array('paramName' => 'cash_detail', 'mandatory' => true, 'dataset' => $OBJ->arrDataDetail, 'detail' =>  $bankDetail)
));
    
require_once '_process.php';
     
?>