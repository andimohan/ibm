<?php

require_once '../../_config.php';  
require_once '_include.php';
 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/SalesOrderSubscription.class.php';    
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ItemUnit.class.php';  
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Service.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Customer.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Employee.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/JobDetails.class.php';
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Item.class.php';


$OBJ = new SalesOrderSubscription(); 
$arrTemp = $OBJ->getInvoicePeriode();
$arrTemp = array_column($arrTemp, 'pkey','name');  
$arrInvoicePeriode = array();
foreach($arrTemp as $key=>$row)
 $arrInvoicePeriode[strtolower($key)] = $row;

$monthlyDetail = array( 
    'pkey' => array('paramName' => 'pkey'),
    'itemkey' =>  array('paramName' => 'service_name', 'mandatory' => true, 'ref' => array('obj' =>  new Service() )), 
    'qty' =>  array('paramName' => 'qty', 'mandatory' => true),
    'unitkey' =>  array('paramName' => 'unit_name', 'ref' => array('obj' =>  new ItemUnit() )),
    'priceinunit'  =>  array('paramName' => 'selling_price','mandatory' => true )
);
  
     
$API_FIELDS = array_merge($API_FIELDS,array(
                'code' =>   array('paramName' => 'code'),  
                'trdate'  =>  array('paramName' => 'date','mandatory' => true ), 
                'warehousekey'  =>  array('paramName' => 'warehouse_id','mandatory' => true,  'ref' => array('obj' => new Warehouse(), 'field' => 'code' )), 
                'customerkey'  =>  array('paramName' => 'customer_id','mandatory' => true, 'ref' => array('obj' => new Customer(), 'field' => 'code' )),  
                'employeekey'  =>  array('paramName' => 'employee_id', 'ref' => array('obj' => new Employee(), 'field' => 'code' )),  
                'periodekey'  =>  array('paramName' => 'invoice_periode' , 'ref' => array('dataset' => $arrInvoicePeriode)),  
                'product'  =>  array('paramName' => 'product'), 
                'trdesc'  =>  array('paramName' => 'description'),
                'jobdetailskey'  =>  array('paramName' => 'jobdetails_id', 'ref' => array('obj' => new JobDetails(), 'field' => 'code' )),
                'service_detail' =>  array('paramName' => 'service_detail', 'mandatory' => true, 'isDetail' => true, 'dataset' => $OBJ->arrMonthly, 'detail' =>  $monthlyDetail),
  
            )); 
  
require_once '_process.php';
     
?>