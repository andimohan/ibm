<?php 
require_once '../_config.php'; 
require_once '../_include-v2.php';  

includeClass(array('Downpayment.class.php', 'CustomerDownpayment.class.php', 'SupplierDownpayment.class.php'));

$customerDownpayment = new CustomerDownpayment();
$supplierDownpayment = new SupplierDownpayment();

$obj = $customerDownpayment;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 2');  

include 'ajax-general.php';

if (isset($_GET) && !empty($_GET['action'])) {
			switch ($_GET['action']){  
                case 'getSalesOrder' :   
                         
                    
                    $order = 'order by value asc';
                    
                    $arrCriteria = array(); 
                
                    if ( isset($_GET['term']) && !empty($_GET['term']) ) 
                        array_push ($arrCriteria, '(value like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );
                     
                    $criteria = implode(' and ', $arrCriteria);  
                    $criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  
  
                    $result = $customerDownpayment->getSalesOrder($criteria, $order);
                        
                    echo json_encode($result); 
                    break; 
                    
				case 'getPurchaseOrder' :   


				$order = 'order by value asc';

				$arrCriteria = array(); 

				if ( isset($_GET['term']) && !empty($_GET['term']) ) 
					array_push ($arrCriteria, '(value like '.$obj->oDbCon->paramString('%'.$_GET['term'].'%').')' );

				$criteria = implode(' and ', $arrCriteria);  
				$criteria = (!empty($criteria)) ? ' and ' . $criteria : '';  

				$result = $supplierDownpayment->getPurchaseOrder($criteria, $order);

				echo json_encode($result); 
				break; 
             
            }
}
 
die;
  
?>