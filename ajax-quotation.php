<?php 
require_once '_config.php'; 
require_once '_include-min.php';
require_once '_global.php';

if(!isset($_POST) || empty($_POST['action']))
	die;
	  
$arrayToJs = array();  

switch ( $_POST['action']){ 
	
		case 'add' :      
                        $arr = array();

						foreach ($_POST as $k => $v) {
							$arr[$k] = $v; 
						}
                        $customerkey = (!empty(USERKEY)) ? USERKEY : 1;
           
						$arr['hidCustomerKey'] = $customerkey;
				    
						$arr['code'] = 'xxxxxx';    
                        $trDate = date('d / m / Y');
						$arr['trDate'] = date('d / m / Y');
						$rsCustomer = $customer->getDataRowById($customerkey);
						$rsWarehouse = $warehouse->searchData($warehouse->tableName.'.statuskey',1,true,' order by pkey asc'); 
						$arr['selWarehouseKey'] =  $rsWarehouse[0]['pkey']; 
						 
						$arr['selTermOfPaymentKey'] = 1; 
						$arr['trDesc'] = '';
						$arr['name'] = $rsCustomer[0]['name'].' '.$trDate;
						$arr['createdBy'] = 0;
						$arr['selSalesKey'] = '1';
						$arr['paymentMethodValue'] = 1;
						$arr['paymentMethodKey'] = 1; 
						$arr['fromFE'] = 1;
                        
                        $totalDays = $_POST['totalDays'];
        
                        $arr['qty'] = array(); 
                        $arr['totalDays'] = array();
        
                        for ($i=0;$i<count($arr['hidItemKey']);$i++){
                            $arr['hidDetailKey'][$i] =0;
                            $rsItem = $item->getDataRowById($arr['hidItemKey'][$i]);
                            $arr['selUnit'][$i] = $rsItem[0]['baseunitkey'];
                            $arr['qty'][$i] = $_SESSION[$class->loginSession]['cart'][$i]['qty'];
                            $arr['priceInUnit'][$i] = 0;  
                            $arr['totalDays'][$i] = $totalDays;  
                            $arr['selTimeUnit'][$i] = 8001;  
                        }
        
						$arrayToJs = array(); 
						$class->addErrorList($arrayToJs,false, $class->errorMsg['cart'][1]); 	

                        $class->setLog($arr,true);
						if (isset($arr['hidItemKey']) && !empty($arr['hidItemKey']))
							$arrayToJs = $salesRentalQuotation->addData($arr); 
                            
                        $salesOrder->clearTemporaryCart();
						break;			
	
}

echo json_encode($arrayToJs); 
  
?>
