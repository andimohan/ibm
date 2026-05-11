<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Downpayment.class.php','SupplierDownpayment.class.php')); 
$supplierDownpayment = createObjAndAddToCol(new SupplierDownpayment());

$obj = $supplierDownpayment;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php'; 

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                 
                case 'getDownpaymentForAP' : 
                    
                    if (!isset($_GET) ||  empty($_GET['apkey']))  
                        die;  
                      
                    $apKey = json_decode($_GET['apkey']); 

					$currencykey = (!empty($_GET['currencykey'])) ?  $_GET['currencykey']  : '';
                    $rsDP = $obj->getDownpaymentForAP($apKey,$currencykey);
                      
                    echo json_encode($rsDP); 
                    
                    break;

   case 'getTotalOutstanding' : 
                     
                    if (empty($_GET['supplierkey'])) die;
                    
                    $supplierKey = $_GET['supplierkey'];

                    $currencykey = (!empty($_GET['currencykey'])) ? $_GET['currencykey'] : '';

                    $rsDP = $obj->getTotalOutstanding($supplierKey, $currencykey);
                    echo json_encode($rsDP); 
break;
            }
}

die;
  
?>
