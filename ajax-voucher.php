<?php 
require_once '_config.php'; 
require_once '_include-fe-v2.php';

includeClass(array('Voucher.class.php',  
                   'Warehouse.class.php',
				   'VoucherTradePoint.class.php',
				   'PointCashback.class.php',
                  ));

$voucher = new Voucher();
$warehouse = new Warehouse();

$obj = $voucher;   

$arrCriteria = array();  
array_push ($arrCriteria, $obj->tableName.'.statuskey = 1');  

include 'ajax-general.php';

 switch ( $_POST['action']){ 
	/*case 'getAvailableVoucher' : 
	  
        $voucherCategory = array(VOUCHER_CATEGORY['sales'],VOUCHER_CATEGORY['shipment']);
        $voucherType = VOUCHER_TYPE['regular'];
        $customerType = CUSTOMER_TYPE['enduser'];
         
        $voucherCriteria = array(); 
        $voucherCriteria['brandkey'] = array(); 
        $voucherCriteria['itemkey'] =  array(); 
        $voucherCriteria['itemcategorykey'] = array();
        $voucherCriteria['totalsales'] = 0;

        // kalo dr cart, ambil dr session
        if($_POST['cart'] == true){
            
            foreach($_SESSION[$class->loginSession]['cart'] as $cartRow){
                $itemkey = $cartRow['itemkey']; 
                $rsItem = $item->getDataRowById($itemkey);
                $arrItem = $rsItem[0];
                
                if(!in_array( $arrItem['brandkey'], $voucherCriteria['brandkey']))  array_push($voucherCriteria['brandkey'], $arrItem['brandkey']);
                if(!in_array( $arrItem['pkey'], $voucherCriteria['itemkey']))  array_push($voucherCriteria['itemkey'], $arrItem['pkey']);
                if(!in_array( $arrItem['categorykey'], $voucherCriteria['itemcategorykey']))  array_push($voucherCriteria['itemcategorykey'], $arrItem['categorykey']);
                 
                $voucherCriteria['totalsales']  += ($cartRow['qty'] * $arrItem['sellingprice']);
                 
            }
              
        } 
       
        $availableVoucher = $voucher->getAvailableVoucher($voucherCategory,$voucherType,$customerType, $voucherCriteria );
        echo json_encode($availableVoucher);
        break; */
         
//	case 'useVoucher' :         
//                      
//        $voucherkey = (isset($_POST['voucherkey']) && is_numeric($_POST['voucherkey'])) ? $_POST['voucherkey'] : 0;
//        $shippingSeviceKey = (isset($_POST['shippingSeviceKey']) && is_numeric($_POST['shippingSeviceKey'])) ? $_POST['shippingSeviceKey'] : 0;
//        $shippingCost = (isset($_POST['shippingCost']) && is_numeric($_POST['shippingCost'])) ? $_POST['shippingCost'] : 0;
//
//        $arrDetailTransaction = $_SESSION[$class->loginSession]['cart'];
//        $arrShipment = array('shippingSeviceKey' => $shippingSeviceKey, 'shippingCost'=> $shippingCost) ;
//
//        /*if(!in_array( $arrItem['brandkey'], $voucherCriteria['brandkey']))  array_push($voucherCriteria['brandkey'], $arrItem['brandkey']);
//        if(!in_array( $arrItem['pkey'], $voucherCriteria['itemkey']))  array_push($voucherCriteria['itemkey'], $arrItem['pkey']);
//        if(!in_array( $arrItem['categorykey'], $voucherCriteria['itemcategorykey']))  array_push($voucherCriteria['itemcategorykey'], $arrItem['categorykey']);
//         */
//         
//        $rsVoucher = $voucher->getVoucherValue($_POST['voucherkey'],$arrDetailTransaction,$arrShipment);
//
//        echo json_encode($rsVoucher);
//        break; 
		 
		     
	case 'trade' :  
		
		if(empty(USERKEY)) die;
		
		 
		$voucherTradePoint = new VoucherTradePoint();
		 
		$arrVoucherKey = $_POST['hidVoucherKey']; 
		$arrQty = $_POST['qty']; 
		  
		$arr = array();
		
		$arr['code'] = 'xxxxx';
		$arr['trDate'] = date('d / m / Y');
		$arr['selWarehouseKey'] = $warehouse->getDefaultData();
		$arr['hidCustomerKey'] = USERKEY;
		$arr['selStatus'] = 1; 
       	//$arr['hidSaveAndProceed'] = 1; // gk bisa pake, karena gk ad session adminnya
         
		$arr['hidDetailKey'] = array();
		$arr['hidVoucherKey'] = array();
		$arr['qty'] = array(); 
		 
		for($i=0;$i<count($arrVoucherKey);$i++){
			array_push($arr['hidDetailKey'],0);
			array_push($arr['hidVoucherKey'],$arrVoucherKey[$i]);
			array_push($arr['qty'],$arrQty[$i]);
		}
		
		$arrayToJs = $voucherTradePoint->addData($arr);   
         
		if(!empty($arrayToJs[0]['data'])){ 
            $response = $voucherTradePoint->changeStatus($arrayToJs[0]['data']['pkey'], 2, '', false, true);
            //$arrayToJs = array_merge($arrayToJs,$response);  // kalo merge, kebykan status berhasilnya nanti
		}
		 
		echo json_encode($arrayToJs);
        break; 
		 
 	case 'pointcashback' :  
		
		if(empty(USERKEY)) die;
		
		$pointCashback = new PointCashback();
		$customer = new Customer();

		$arr = array();
		
		$arr['code'] = 'xxxxx';
		$arr['trDate'] = date('d / m / Y');
		$arr['selWarehouseKey'] = $warehouse->getDefaultData();
		$arr['hidCustomerKey'] = USERKEY;
		$arr['selStatus'] = 1;  
		 
		if(isset($_POST['hidPoint']) && !empty($_POST['hidPoint'])){
			$point=  $_POST['hidPoint'];
		}else{
			$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString(USERKEY));
			$point = $rsCustomer[0]['point']; 
		}
		
		$arr['point'] = $point;
 
		$arrayToJs = $pointCashback->addData($arr);   
//        $class->setLog($arrayToJs,true);
		 
		echo json_encode($arrayToJs);
        break; 

case 'redeemVoucher' :  
		
		if(empty(USERKEY)) die;
		
		$voucher = new Voucher();
		$voucherTransaction = new VoucherTransaction();

		$arr = array();
		$arrayToJs = array();
		
		$voucherCode = $_POST['vouchercode'];
		 
		$rsVoucher = $voucher->searchData('','',true, ' and '.$voucher->tableName.'.typekey = '.$voucher->oDbCon->paramString(VOUCHER_TYPE['claim']).' 
													 and ' .$voucher->tableName.'.code ='.$voucher->oDbCon->paramString($voucherCode));
		
		if (!empty($rsVoucher)) {

			// cari sudah pernah ad claim blm
			$rsVoucherTransaction = $voucherTransaction->searchData('','',true,' and '.$voucherTransaction->tableName.'.statuskey in (1,2,3) 
																			 and '.$voucherTransaction->tableName.'.customerkey = '.$voucherTransaction->oDbCon->paramString(USERKEY).' 
																			 and '.$voucherTransaction->tableName.'.refvoucherkey ='.$voucherTransaction->oDbCon->paramString($rsVoucher[0]['pkey']));

		
			if (!empty($rsVoucherTransaction)) { 		
				$class->addErrorList($arrayToJs,false, $class->lang['voucherClaimFailed']);
			} else {

				$arr['code'] = 'xxxxx';
				$arr['trDate'] = date('d / m / Y');
				$arr['selWarehouse'] = $warehouse->getDefaultData();
				$arr['hidVoucherKey'] = $rsVoucher[0]['pkey'];
				$arr['hidCustomerKey'] = USERKEY;
				$arr['value'] = $rsVoucher[0]['value'];
				$arr['selDiscountType'] = $rsVoucher[0]['discounttype'];
				$arr['minAmount'] = $rsVoucher[0]['minamount'];
				$arr['maxDiscount'] = $rsVoucher[0]['maxdiscount']; 
				$arr['expDate'] = $obj->formatDBDate($rsVoucher[0]['enddate'], 'd / m / Y'); 
				 
				$arr['selStatus'] = 1;  

				$arrayToJs = $voucherTransaction->addData($arr);  
				
				if(!empty($arrayToJs[0]['data'])){ 
					$arrayToJs = $voucherTransaction->changeStatus($arrayToJs[0]['data']['pkey'], 2, '', false, true);
				}
				if (!$arrayToJs[0]['valid']) {
					$class->addErrorList($arrayToJs,false, $class->lang['voucherClaimFailed']);
				} else {
					$arrayToJs[0]['message'] = $class->lang['voucherSuccessfullyClaimed'];
				}
			}

		} else {	
			$class->addErrorList($arrayToJs,false, $class->lang['voucherClaimFailed']);
		}
		 
		echo json_encode($arrayToJs);
        break;

	case 'getVoucherClaim' : 	  
		$voucherTransaction = new VoucherTransaction();

        $availableVoucher = $voucherTransaction->getAvailableVoucher(USERKEY);

        echo json_encode($availableVoucher);
        break;
 }

die;
  
?>
