<?php

require_once '../../_config.php';  
require_once '_include.php';

require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashOut.class.php'; 
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CostCashOut.class.php';      
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/ChartOfAccount.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/CashBank.class.php';   
require_once DOC_ROOT. 'include/'.CLASS_VERSION.'/Warehouse.class.php';  
 
$OBJ = new CashOut();
$chartOfAccount = new ChartOfAccount();
    
$bankDetail = array( 
    'pkey' => array('paramName' => 'pkey'),
    'amount' =>  array('paramName' => 'amount' ,'mandatory' => true),
    'trdesc' => array('paramName' => 'description'),
);

if($OBJ->useMasterCost)
	$bankDetail['costkey'] = array('paramName' => 'cost_name',  'ref' => array('obj' =>  new CostCashOut()));
else
	$bankDetail['coakey'] = array('paramName' => 'cost_name',  'ref' => array('obj' => $chartOfAccount));

$API_FIELDS = array_merge($API_FIELDS,array(
    'code' =>   array('paramName' => 'code'), 
    'coakey'  =>  array('paramName' => 'coa_name','mandatory' => true, 'ref' => array('obj' => $chartOfAccount)),
    'trdate'  =>  array('paramName' => 'date','mandatory' => true ), 
    'recipientname'  =>  array('paramName' => 'recipient_name'), 
    'trdesc'  =>  array('paramName' => 'note'), 
    'cash_detail' =>  array('paramName' => 'cash_detail', 'mandatory' => true, 'dataset' => $OBJ->arrDataDetail, 'detail' =>  $bankDetail)
));
    
require_once '_process.php';
     
?>