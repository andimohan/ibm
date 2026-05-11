<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Downpayment.class.php','CustomerDownpayment.class.php'));
$customerDownpayment = createObjAndAddToCol(new CustomerDownpayment());
$truckingServiceOrderInvoice = createObjAndAddToCol(new TruckingServiceOrderInvoice());
$emklOrderInvoice = createObjAndAddToCol(new EMKLOrderInvoice());

$obj = $customerDownpayment;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  

$fieldValue = $obj->tableName.'.code';

include 'ajax-general.php'; 

if (isset($_GET) && !empty($_GET['action'])) {
			switch ( $_GET['action']){  
                 
                case 'getDownpaymentForAR' : 
                    
                    if (!isset($_GET) ||  empty($_GET['arkey']))  
                        die;  
                      
                    $arKey = json_decode($_GET['arkey']); 

					$currencykey = (!empty($_GET['currencykey'])) ? $_GET['currencykey'] : '';
                    $rsDP = $obj->getDownpaymentForAR($arKey,$currencykey);
                      
                    echo json_encode($rsDP); 
                    
                    break; 
                    
                    
                case 'getDownpaymentForTruckingServiceOrderInvoice' : 
                    
                    if (!isset($_GET) ||  empty($_GET['sokey']))  
                        die;  
                     
                    $soKey = json_decode($_GET['sokey']); 
					$currencykey = (isset($_GET['currencykey']) && !empty($_GET['currencykey'])) ? $_GET['currencykey'] : '';
                    $rsDP = $obj->getDownpaymentForTruckingServiceOrderInvoice($soKey,$currencykey);
                      
                    echo json_encode($rsDP); 
                    
                    break; 



                case 'getTotalOutstanding' : 
                
                    if (empty($_GET['customerkey'])) die;
                    
                    $customerKey = $_GET['customerkey'];
                    
                    $currencykey = (!empty($_GET['currencykey'])) ? $_GET['currencykey'] : '';
                    $rsDP = $obj->getTotalOutstanding($customerKey, $currencykey);
                    echo json_encode($rsDP); 
                    
                    break;
            }
}

die;
  
?>
