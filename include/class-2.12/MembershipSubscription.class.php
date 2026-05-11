<?php
  
class MembershipSubscription extends BaseClass{ 
  

    function __construct(){

            parent::__construct();


            $this->tableName = 'membership_subscription';
            //$this->tableNameDetail = 'membership_subscription_detail';
            $this->tableCustomer = 'customer';
			$this->tableMembershipLevel = 'membership_level';
            $this->tableWarehouse = 'warehouse'; 
       		$this->tableJobPosition = 'job_position';
            $this->tableStatus = 'transaction_status';
	    	$this->tableBusinessCategory = 'business_category';
            $this->tablePayment = 'membership_subscription_payment';  
            $this->tableCurrency = 'currency';
            //$this->tablePaymentConfirmation = 'payment_confirmation'; 
		    $this->tableTermOfPayment = 'term_of_payment'; 
            $this->tableARStatus = 'ar_status'; 
            $this->isTransaction = true; 		

			$this->newLoad = true;
            $this->securityObject = 'MembershipSubscription';   

            $this->arrLinkedTable = array(); 
            $defaultFieldName = 'refkey';
            array_push($this->arrLinkedTable, array('table'=>'ar','field'=>$defaultFieldName));  

        
            $this->arrPaymentDetail = array(); 
            $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
            $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
            $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
            $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 

//            $this->arrVoucherDetail = array(); 
//            $this->arrVoucherDetail['pkey'] = array('hidDetailVoucherKey');
//            $this->arrVoucherDetail['refkey'] = array('pkey', 'ref');
//            $this->arrVoucherDetail['amount'] = array('voucherAmount',array('datatype' => 'number','mandatory'=>true));
//            $this->arrVoucherDetail['voucherkey'] = array('hidVoucherKey',array('mandatory'=>true)); 

            $arrDetails = array();
           //array_push($arrDetails, array('dataset' => $this->arrDataDetail));
            array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
//            array_push($arrDetails, array('dataset' => $this->arrVoucherDetail, 'tableName' => $this->tableVoucherDetail));

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            $this->arrData['code'] = array('code');
            $this->arrData['customcodekey'] = array('selCustomCode'); 
            $this->arrData['trdate'] = array('trDate','datetime');
            $this->arrData['warehousekey'] = array('selWarehouseKey');
            $this->arrData['customerkey'] = array('hidCustomerKey');
            $this->arrData['termofpaymentkey'] = array('selTermOfPaymentKey');
            $this->arrData['trdesc'] = array('trDesc');
            $this->arrData['subtotal'] = array('subtotal','number');
            $this->arrData['finaldiscounttype'] = array('selFinalDiscountType','number');
            $this->arrData['finaldiscount'] = array('finalDiscount','number');
            $this->arrData['beforetaxtotal'] = array('beforeTaxTotal','number');
            $this->arrData['ispriceincludetax'] = array('isPriceIncludeTax');
            $this->arrData['taxpercentage'] = array('taxPercentage','number');
            $this->arrData['taxvalue'] = array('taxValue','number');
            $this->arrData['grandtotal'] = array('grandtotal','number');
            $this->arrData['totalpayment'] = array('totalPayment','number');
            $this->arrData['balance'] = array('balance','number'); 
            $this->arrData['statuskey'] = array('selStatus');
            $this->arrData['point'] = array('point','number');
            $this->arrData['pointvalue'] = array('pointValue','number');
            $this->arrData['refcode'] = array('refCode');
            //$this->arrData['referralkey'] = array('referralkey');    // ambil dr customernya aj biar aman, karena bisa reset referralkey 
			$this->arrData['membershiplevelkey'] = array('selMembershipLevel');  
			$this->arrData['activeperiodmonth'] = array('activePeriod','number');
			$this->arrData['validuntil'] = array('validuntil','date');
			$this->arrData['checksum'] = array('checksum');
		    $this->arrData['currencykey'] = array('hidCurrencyKey');
		 
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customercode','title' => 'customerCode','dbfield' => 'customercode','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
            array_push($this->arrDataListAvailableColumn, array('code' => 'membershiplevel','title' => 'membership','dbfield' => 'membershiplevel','default'=>true, 'width' => 100, 'align' => 'center'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'activeperiod','title' => 'activePeriod','dbfield' => 'activeperiodmonth','default'=>true, 'width' => 100, 'align' => 'right'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencycode','default'=>true, 'width' => 60, 'align' => 'center'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'price','dbfield' => 'grandtotal','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            array_push($this->arrDataListAvailableColumn, array('code' => 'paymentsuccessicon','title' => '[icon]paymentGateway', 'dbfield' => 'paymentsuccessicon',  'width' => 40, 'align' => 'center'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'paidstatus','title' => 'payment', 'dbfield' => 'paidstatusname',  'width' => 40));
            
            
//            $this->printMenu = array();  
//            array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print'] . ' ' .$this->lang['invoice'],  'icon' => 'print', 'url' => 'print/salesOrder'));
//            array_push($this->printMenu,array('code' => 'printDeliveryNotes', 'name' => $this->lang['print'] . ' ' .$this->lang['deliveryNotes'],  'icon' => 'print', 'url' => 'print/salesOrderDelivery'));
//            array_push($this->printMenu,array('code' => 'printShippingLabel', 'name' => $this->lang['print'] . ' ' .$this->lang['shippingLabel'],  'icon' => 'print', 'url' => 'print/salesLabel'));
//        
              
            $this->includeClassDependencies(array(
                   'TermOfPayment.class.php', 
                   'Warehouse.class.php',  
                   'PaymentMethod.class.php', 
                   'City.class.php', 
                   'Customer.class.php',  
                   'RewardsPoint.class.php', 
                   'AP.class.php', 
                   'APCustomerCommission.class.php', 
                   'COALink.class.php',
                   'VoucherTransaction.class.php', 
                   'PaymentConfirmation.class.php',
                   'AR.class.php',
                   'ARPayment.class.php', 
                   'ChartOfAccount.class.php',
                   'GeneralJournal.class.php',
                   'MembershipLevel.class.php',
				   'MeetingSchedule.class.php',
				   'CustomerFeatures.class.php',
                   'Currency.class.php'
            ));  
		
		
	    	$this->activeModule = $this->isActiveModule(array('activityLog'));
		
			if( $this->activeModule['activitylog']){  
			   $this->includeClassDependencies(array(
					'ActivityLog.class.php',  
				));
			}       

            $this->overwriteConfig();
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.code as customercode,
               '.$this->tableCustomer.'.name as customername, 
               '.$this->tableCustomer.'.phone as customerphone, 
               '.$this->tableCustomer.'.mobilecode as customermobilecode, 
               '.$this->tableCustomer.'.mobile as customermobile, 
               '.$this->tableCustomer.'.email as customeremail,  
               '.$this->tableCustomer.'.langkey as customerlangkey, 
               '.$this->tableStatus.'.status as statusname, 
               '.$this->tableTermOfPayment.'.code as termofpaymentcode,
               '.$this->tableTermOfPayment.'.name as termofpaymentname,
               '.$this->tableARStatus.'.status as paidstatusname,
			   '.$this->tableMembershipLevel.'.name as membershiplevel,
			   '.$this->tableCurrency.'.code as currencycode,
               IF(paymentgatewaysuccess=1, "<i class=\"fas fa-check text-green-avocado\"></i>", "") as paymentsuccessicon
            FROM 
                '.$this->tableStatus.',   
                '.$this->tableCustomer.',   
				'.$this->tableMembershipLevel.',
                '.$this->tableName.' 
                    left join '.$this->tableTermOfPayment.' on '.$this->tableName.'.termofpaymentkey = '.$this->tableTermOfPayment.'.pkey
                    left join '.$this->tableARStatus.' on  '.$this->tableName.'.paidstatuskey =  '.$this->tableARStatus.'.pkey
                    left join '.$this->tableCurrency.' on  '.$this->tableName.'.currencykey =  '.$this->tableCurrency.'.pkey
            WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                  '.$this->tableName.'.membershiplevelkey = '.$this->tableMembershipLevel.'.pkey and
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey  
        ' .$this->criteria ;
		
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;
   
        return $sql;
    }
	  
    function reCountSubtotal($arrParam){

            $isPriceIncludeTax = (!empty($arrParam['chkIncludeTax'])) ? 1 : 0;  

//			$membershipLevel  = new MembershipLevel();
//			$rsMembership = $membershipLevel->getDataRowById($arrParam['selMembershipLevel']);
//          $subtotal = (!empty($rsMembership)) ? $rsMembership[0]['sellingprice'] : 0  ;		
            $arrTotalSubscription = $this->calculateReSubscriptionTotal($arrParam['hidCustomerKey'],$arrParam['selMembershipLevel'],$arrParam['hidCurrencyKey']);
        	
			$subtotal = $arrTotalSubscription['totalAmount'];
			$activePeriod = $arrTotalSubscription['totalMonth'];
		
             
            $grandtotal = $subtotal;
 
			//$arrVoucherKey = $arrParam['hidVoucherKey']; 
            $taxValue = $this->unFormatNumber($arrParam['taxValue']);  
            $finalDiscount = $this->unFormatNumber($arrParam['finalDiscount']); 
            $finalDiscountType = $arrParam['selFinalDiscountType']; 
            $taxPercentage = $this->unFormatNumber($arrParam['taxPercentage']);  
            $shipmentFee = 0 ; //$this->unFormatNumber($arrParam['shipmentFee']); 
            $etcCost = $this->unFormatNumber($arrParam['etcCost']); 
        
            $arrVoucherDetail = array();
		 
            if ($finalDiscount != 0){
                if ($finalDiscountType == 2)
                    $finalDiscount = $finalDiscount/100 * $grandtotal;
            } 
		
			// hitung ulang nilai voucher
//			$voucherValue = 0;
//			$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
//			
//			if($useVoucherPoint == 1){  
//				$voucherTransaction = new VoucherTransaction();
//				for ($i=0;$i<count($arrVoucherKey);$i++){
//					$arrVoucherDetail[$i]['voucherAmount'] = $voucherTransaction->calculateVoucherValue($arrVoucherKey[$i], $subtotal - $finalDiscount);
//					$voucherValue += $arrVoucherDetail[$i]['voucherAmount'];
//				}
//			}
			     
			$point  = $this->unFormatNumber($arrParam['point']);   
			$pointValue = $point * $this->loadSetting('rewardsPointUnitValue');
			
            $totalFinalDiscountAndPointValue = $finalDiscount + $pointValue ;
 
            $beforeTaxTotal = $subtotal - $totalFinalDiscountAndPointValue;
            $grandtotal = $beforeTaxTotal;

            if ($isPriceIncludeTax == false) {
                    $taxValue = $beforeTaxTotal * $taxPercentage / 100;
                    $taxValue = round($taxValue); // kalo ad koma, nilainya gantung di AR nanti
                    $grandtotal += $taxValue;
            }else{
                    $taxValue = ($taxPercentage/(100 + $taxPercentage)) * $grandtotal;   
                    $beforeTaxTotal = $grandtotal - $taxValue ;
            }

            $grandtotal +=  $shipmentFee + $etcCost;


            $balance = 0;
            $totalPayment = 0; 

            $termOfPayment = new TermOfPayment();
            $rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
            if ($rsTOP[0]['duedays'] == 0){ 
                $payment = $arrParam['paymentMethodValue'];
                for($i=0;$i<count($payment);$i++){
                    $totalPayment += $this->unFormatNumber($payment[$i]);
                } 
            }


            $balance = $totalPayment - $grandtotal; 

            $reCountResult = array();
            $reCountResult['subtotal'] = $subtotal;
            $reCountResult['activePeriod'] = $activePeriod;
            $reCountResult['beforeTaxTotal'] = $beforeTaxTotal;
            $reCountResult['isPriceIncludeTax'] = $isPriceIncludeTax;
            $reCountResult['taxValue'] = $taxValue;
            $reCountResult['grandtotal'] = $grandtotal;
            $reCountResult['totalPayment'] = $totalPayment;
            $reCountResult['balance'] = $balance;
            $reCountResult['pointValue'] = $pointValue; 

            return $reCountResult;

    } 
	
    function validateForm($arr,$pkey = ''){ 
            $arrayToJs = parent::validateForm($arr,$pkey); 

			$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
		
            $customerkey = $arr['hidCustomerKey'];   
            
            //validasi kalo status gk menunggu gk bisa edit 
            if (!empty($pkey)){
                $rs = $this->getDataRowById($pkey);
                if ($rs[0]['statuskey'] <> 1){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
                }
            }  

            if(empty($customerkey)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
            }
  
 
			// validasi voucher
		
			// harus bedain, kalo mengecualikan voucher sendiri, tetep harus cek voucher sudah digunakan atau blm..
//			$hasVoucher = false;
//			foreach($arrVoucherKey as $voucherkey)
//				if(!empty($voucherkey)) $hasVoucher = true;
//					
//			if($useVoucherPoint == 1 && $hasVoucher){ 
				
//				$voucherTransaction = new VoucherTransaction();
//				$rsVoucherTransaction = $voucherTransaction->searchDataRow(array($voucherTransaction->tableName.'.pkey', $voucherTransaction->tableName.'.customerkey', $voucherTransaction->tableName.'.expdate', $voucherTransaction->tableName.'.statuskey'),
//																		  ' and '.$voucherTransaction->tableName.'.pkey in ('.$this->oDbCon->paramString($arrVoucherKey,',').')  
//																		  	and '.$voucherTransaction->tableName.'.reftranskey in (0, '.$this->oDbCon->paramString($pkey).')
//																		 ');
//				// status harus cek manual sepertinya,
//				// karena kalo dipake transaksi sendiri, statusnay sudah 3
//				
//				$rsVoucherTransaction = array_column($rsVoucherTransaction,null,'pkey'); 
//				// voucher harus punya customer yg sama
//				$totalVoucher = count($arrVoucherKey);
//				for($i=0;$i<$totalVoucher;$i++) { 
//						
//					if (!isset($rsVoucherTransaction[$arrVoucherKey[$i]])){ 
//						$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][6]); 	
//					}else{ 
//						$rsVoucher = $rsVoucherTransaction[$arrVoucherKey[$i]];
//					
//						if( $arrVoucherKey[$i] <> $rsVoucher['pkey']  && $rsVoucher['statuskey'] <> 2)
//							$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][3]); 	 
//						 
//						if($rsVoucher['customerkey'] <> $customerkey)
//							$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][5]); 	 
//						
//
//						// voucher blm epxired
//						$expDate = $this->formatDBDate($rsVoucher['expdate'],'d / m / Y'); 
//						$transDate = $arr['trDate']; 
//						$dateDiff = $this->dateDiff($expDate,$transDate);
//						if($dateDiff > 0)
//							$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][8]); 	 
//
//					}  
//					
//				}
// 
//				
//				$minLevel = $this->loadSetting('minMembershipLevelToUseVoucher');
//				$customer = new Customer();
//				$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel'),
//													   ' and '.$customer->tableName.'.pkey = ' . $this->oDbCon->paramString($customerkey));
//				
//				// keanggotaan sudah boleh menggunakan voucher
//				if($rsCustomer[0]['membershiplevel'] < $minLevel)
//					$this->addErrorList($arrayToJs,false, $this->errorMsg['voucher'][7]);
//				
//			}
			
            return $arrayToJs;
    }
 

    function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $rewardsPoint = new RewardsPoint();
        $warehouse = new Warehouse();  
        $coaLink = new COALink();
        $voucherTransaction = new VoucherTransaction(); 
		$customer = new Customer(); 
		
		$customerkey = $rsHeader[0]['customerkey'];
		$rsCustomer = $customer->getDataRowById($customerkey);

		$useVoucherPoint = $this->loadSetting('transactionVoucherPoint');
		
        $rsPayment = $this->getPaymentMethodDetail($id); 
		//$rsVoucher =  $this->getVoucherDetail($id); 
			
        $termOfPayment = new TermOfPayment();
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']);  
        $isCash = ($rsTOP[0]['duedays'] == 0) ? true : false;   
		
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];

        $balance = $totalPayment - $rsHeader[0]['grandtotal'];   
 
        if ($isCash){ 
            $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
            if($balance < ($thresholdDiscount * -1)) 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
            else if ($balance > $thresholdDiscount)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 
        }else{
              
            //validasi creditlimit
            // masi harus ditambahkan dengan JO yg masih menggantung
            
            if ($rsCustomer[0]['creditlimit'] > 0){  
                $total = $this->unFormatNumber($rsHeader[0]['grandtotal']);
                if ($customer->willExceedCreditLimit($customerkey,$total)){
                     $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['creditlimit'][1]);
                }
            }
            
        }
         
		
        // validasi point
        $point = $rsHeader[0]['point'];
        $currentPoint = $rsCustomer[0]['point'];
        if ($point > $currentPoint)
            $this->addErrorLog(false, $this->errorMsg['point'][3]);

        if (USE_GL){
                $arrCOA = array();
                array_push($arrCOA, 'salesretail' , 'taxout', 'otherrevenue', 'hpp' , 'inventory' , 'inventorytemp', 'salesretaildiscount'); 
                for ($i=0;$i<count($arrCOA);$i++){
                    $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
                }   

                if ($isCash){
                    for($i=0;$i<count($rsPayment); $i++){ 
                        if ($rsPayment[$i]['amount'] > 0 ){ 
                            $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                            if (empty($rsCOA))	
                                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                        }
                    } 
                }else{ 
                        // validasi COA piutang  
                        $rsCOA = $coaLink->getCOALink ('ar', $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                        if (empty($rsCOA))	
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]);
                }
 
         } 
         
		 
		// validasi voucher
		
//		$arrVoucherKey = array_column($rsVoucher,'voucherkey'); 
//		$totalVoucher = count($arrVoucherKey);
//		if($useVoucherPoint == 1 && $totalVoucher > 0){
//			
//			
//			$voucherTransaction = new VoucherTransaction();
//			$rsVoucherTransaction = $voucherTransaction->searchDataRow(array($voucherTransaction->tableName.'.pkey', $voucherTransaction->tableName.'.customerkey', $voucherTransaction->tableName.'.expdate'),
//																	  ' and '.$voucherTransaction->tableName.'.pkey in ('.$this->oDbCon->paramString($arrVoucherKey,',').')  
//																	  	and '.$voucherTransaction->tableName.'.reftranskey in (0, '.$this->oDbCon->paramString($id).')
//																	');
//		 
//			$rsVoucherTransaction = array_column($rsVoucherTransaction,null,'pkey');
//			
//			// voucher harus punya customer yg sama 
//			for($i=0;$i<$totalVoucher;$i++) { 
//
//				if (!isset($rsVoucherTransaction[$arrVoucherKey[$i]])){ 
//					$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][6]); 	
//				}else{ 
//					$rsVoucher = $rsVoucherTransaction[$arrVoucherKey[$i]];
//					
//					if( $arrVoucherKey[$i] <> $rsVoucher['pkey']  && $rsVoucher['statuskey'] <> 2)
//						$this->addErrorLog(false, $this->errorMsg['voucher'][3]); 	 
//
//					if($rsVoucher['customerkey'] <> $customerkey)
//						$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][5]); 	 
// 
//					// voucher blm epxired
//					$expDate = $this->formatDBDate($rsVoucher['expdate'],'d / m / Y'); 
//					$transDate = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
//					$dateDiff = $this->dateDiff($expDate,$transDate);
//					if($dateDiff > 0)
//						$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][8]); 	 
//
//				}  
//
//			}
//
//
//			$minLevel = $this->loadSetting('minMembershipLevelToUseVoucher');
//			$customer = new Customer();
//			$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel'),
//												   ' and '.$customer->tableName.'.pkey = ' . $this->oDbCon->paramString($customerkey));
//
//			// keanggotaan sudah boleh menggunakan voucher
//			if($rsCustomer[0]['membershiplevel'] < $minLevel)
//				$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['voucher'][7]);
//
//		}

 
    }
  
    function updatePaidStatus($pkey,$paidStatus){
        $sql = 'update '.$this->tableName.' set paidstatuskey = ' . $this->oDbCon->paramString($paidStatus) .' where pkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);
    }

    function confirmTrans($rsHeader){  
         
        $id = $rsHeader[0]['pkey']; 
       
        $coaLink = new COALink(); 
        $termOfPayment = new TermOfPayment();
        $customer = new Customer();
		$apCustomerCommission = new APCustomerCommission();
		$customerFeatures = new CustomerFeatures();
			 
		$customerkey = $rsHeader[0]['customerkey'];
		$currencykey = $rsHeader[0]['currencykey'];
		$membershipLevelKey = $rsHeader[0]['membershiplevelkey'];
			
        $rsTOP = $termOfPayment->getDataRowById($rsHeader[0]['termofpaymentkey']); 
		$isCash = ($rsTOP[0]['duedays'] == 0) ? true : false; 
 
		$refTableKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
			
        // MENGHITUNG PAYMENT
        if ($isCash){ 
            $rsPayment = $this->getPaymentMethodDetail($id);    

            for($i=0;$i<count($rsPayment); $i++){  
                  if (USE_GL) {
                       $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
				       $coakey = $rsPaymentCOA[0]['coakey']; 
				   }else{
				       $coakey = $rsPayment[$i]['paymentkey'];
				   } 
             } 
            
            $this->updatePaidStatus($id,3);
            
        }else{
            //update AR
            $ar = new AR();

            $arrParam = array();	
 
            $arrParam['code'] = 'xxxxxx';
            $arrParam['hidCustomerKey'] = $customerkey;
            $arrParam['hidSalesKey'] = $rsHeader[0]['saleskey'];
            $arrParam['hidRefKey'] = $id;
            $arrParam['hidRefHeaderKey'] = $id;
            $arrParam['hidRefCode'] =  $rsHeader[0]['code']; 
            $arrParam['hidRefCode2'] =  $rsHeader[0]['refcode'];  
            $arrParam['hidRefTable'] = $refTableKey;
            $arrParam['hidRefDate'] =   $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y'); 
            $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
            $arrParam['selCurrency'] = $rsHeader[0]['currencykey'];
            $arrParam['selARType'] = 1; 
            $arrParam['amount'] = abs($rsHeader[0]['grandtotal']);
            $arrParam['trDesc'] = '';
            $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
            $date = new DateTime($rsHeader[0]['trdate']);
            $date->add(new DateInterval('P'.$rsTOP[0]['duedays'].'D'));
            $arrParam['dueDate'] = $date->format('d / m / Y');// date ('d / m / Y', mktime(0, 0, 0, date("m")  , date("d")+$rsTOP[0]['duedays'], date("Y")));
            $arrParam['createdBy'] = 0;
            $arrParam['islinked'] = 1; 
            $arrParam['overwriteGL'] = 1;

            $arrayToJs = $ar->addData($arrParam); 
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);    
        }
        // END            
 	
		// update ulang membershipscubscription
		$arrSubscription = $this->calculateReSubscriptionTotal($customerkey, $membershipLevelKey, $currencykey);
		if($arrSubscription['isNew']){
			// kalo user baru atau yg membershipnya sudah expired
			// tgl expired dihitung dr tgl konfirmasi pembayaran (hari ini) 
			$date = new DateTime();
            $date->add(new DateInterval('P'.$rsHeader[0]['activeperiodmonth'].'M'));
			$expdateuntil = $date->format('d / m / Y');
		}else{
			// perlu cek perpanang sampe akhir bulan atau sampe periode berikutnya
			$expdateuntil = $this->formatDBDate($arrSubscription['expDateUntil']);
		}
		
		// update membership
		$sql = 'update '.$this->tableCustomer.' set 
					membershiplevel = '.$this->oDbCon->paramString($membershipLevelKey).', 
					expdate = '.$this->oDbCon->paramDate($expdateuntil).' 
				where pkey = ' .$this->oDbCon->paramString($customerkey);
		$this->oDbCon->execute($sql);
		
		
		// update status kalo blm aktivasi
		$sql = 'update '.$this->tableCustomer.' set 
					statuskey = 2, 
					activationdate = now() 
				where statuskey = 1 and pkey = ' .$this->oDbCon->paramString($customerkey);
		$this->oDbCon->execute($sql);
		
		
		// update expdate utk transaksi
		$sql = 'update '.$this->tableName.' set 
						expdateuntil =  '.$this->oDbCon->paramDate($expdateuntil).' 
				where pkey = ' .$this->oDbCon->paramString($id);
		$this->oDbCon->execute($sql);
		
		
		if($rsHeader[0]['point'] > 0){ 
			$rewardsPoint = new RewardsPoint(); 
			$rewardsPoint->deductPoint($customerkey, $rsHeader[0]['point'], array('pkey' => $id, 'refTableType' => $refTableKey));
		} 
		
		// khusus add new
		// member yg telat perpanjang akan dianggap pendaftaran baru
		
		if($arrSubscription['isNew']){ 
			$customer->resetReferralKeyIfQuotaExceed($customerkey); 
		}else{
				// nanti perlu diganti cara ngecek transaksi pertamanya
				if($this->activeModule['activitylog']){ 
					$arrActivityLog = array();
					array_push($arrActivityLog, 
											array(  
													'templatekey' => 10, 
													'refkey' => $customerkey,  
													'levelkey' => $rsHeader[0]['membershiplevelkey'],  
													'transkey' => $id
												) 
									); 

					$activityLog = new ActivityLog();
					$activityLog->addNewLog($arrActivityLog);  
				}
  
		}
		 
		
        // kalo ad referral
		$rsCustomer = $customer->getDataRowById($customerkey);
		  
		$membershiplevel = new MembershipLevel();  
		$rsMembershipLevel = $membershiplevel->searchDataRow(array($membershiplevel->tableName . '.commissiontype',
																   $membershiplevel->tableName . '.commissiontotal', 
																   $membershiplevel->tableName . '.hostcommissiontype1',
																   $membershiplevel->tableName . '.hostcommissiontotal1', 
																   $membershiplevel->tableName . '.hostcommissiontype2', 
																   $membershiplevel->tableName . '.hostcommissiontotal2', 
																   $membershiplevel->tableName . '.defaulthostcommissionkey', 
																   $membershiplevel->tableName . '.sellingprice'
																  ), ' and ' . $membershiplevel->tableName . '.pkey=' . $this->oDbCon->paramString($membershipLevelKey));
 
	 
		
		$defaultCustomerKey = $rsMembershipLevel[0]['defaulthostcommissionkey'];
				 
		
		// kalo ad referral dan ad komisi
		$commissionType = $rsMembershipLevel[0]['commissiontype'];
		$commissionValue = $rsMembershipLevel[0]['commissiontotal'];
		$total = ($commissionType == 1) ? $commissionValue : ($commissionValue / 100) * $rsHeader[0]['beforetaxtotal'];

		$referralkey = (!empty($rsCustomer[0]['referralkey'])) ? $rsCustomer[0]['referralkey'] : $defaultCustomerKey;

		if($total > 0){
			// fee referral 
			$arrAp = array();
			$arrAp['code'] = 'xxxxx';
			$arrAp['hidCustomerKey'] = $referralkey;
			$arrAp['selWarehouse'] = $rsHeader[0]['warehousekey'];
			$arrAp['hidRefKey'] = $id;
			$arrAp['hidRefCode'] = $rsHeader[0]['code'];
			$arrAp['hidRefDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
			$arrAp['hidRefTable'] = $refTableKey;
            $arrAp['selCurrency'] = $rsHeader[0]['currencykey'];
			$arrAp['amount'] = $total;
			$arrAp['trDesc'] = '';
			$arrAp['selAPType'] = AP_TYPE['salesCommission'];
			$arrAp['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');   
			$arrAp['dueDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
			$arrAp['islinked'] = 1; 
			$arrAp['overwriteGL'] = 1;
			$arrAp['selCurrency'] = $rsHeader[0]['currencykey'];
			$arrAp['currencyRate'] = 1;
			$arrAp['amountIDR'] = $total;

			$arrayToJs = $apCustomerCommission->addData($arrAp);  
			
			if($this->activeModule['activitylog']){ 
				$arrActivityLog = array();
				array_push($arrActivityLog, 
										array( 
												'modulekey' => 5, 
												'templatekey' => 18, 
												'refkey' => $referralkey,  
												'userkey' => $customerkey,  
												'amount' => $total,  
												'transkey' => $id
											) 
								); 

				$activityLog = new ActivityLog();
				$activityLog->addNewLog($arrActivityLog);  
			}
			

			if (!$arrayToJs[0]['valid'])
				throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
		}

        
		// fee host, kalo gk ada host, lempar ke default customer
		// HOST cuma sekali dpt saja  
		// $rsCustomer[0]['hostlevelkey'] buat jaga2
		if($this->isFirstSubscription($rsCustomer[0]['pkey'],$id)){  
				// cari host yg terakhir user ikut
				$meetingSchedule = new MeetingSchedule();
				$rsMeeting = $meetingSchedule->getLatestCheckedIn($rsCustomer[0]['pkey']);  
				$hostuserkey = (!empty($rsMeeting)) ? $rsMeeting[0]['hostkey'] : $defaultCustomerKey;
				$rsHost = $customer->getDataRowById($hostuserkey); 
				$hostLevelKey = $rsHost[0]['hostlevelkey'];
			 
				$commissionType = $rsMembershipLevel[0]['hostcommissiontype'.$hostLevelKey];
				$commissionValue = $rsMembershipLevel[0]['hostcommissiontotal'.$hostLevelKey]; 
				$total = ($commissionType == 1) ? $commissionValue : ($commissionValue / 100) * $rsHeader[0]['beforetaxtotal'];
				  
				if($total > 0){
					$arrAp = array();
					$arrAp['code'] = 'xxxxx';
					$arrAp['hidCustomerKey'] = $hostuserkey;
					$arrAp['selWarehouse'] = $rsHeader[0]['warehousekey'];
					$arrAp['hidRefKey'] = $id;
					$arrAp['hidRefCode'] = $rsHeader[0]['code'];
					$arrAp['hidRefDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
					$arrAp['hidRefTable'] = $refTableKey;
					$arrAp['amount'] = $total;
					$arrAp['trDesc'] = '';
					$arrAp['selAPType'] = AP_TYPE['serviceOutsource'];
					$arrAp['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');   
					$arrAp['dueDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
					$arrAp['islinked'] = 1; 
					$arrAp['overwriteGL'] = 1;
                    $arrAp['selCurrency'] = $rsHeader[0]['currencykey'];
					$arrAp['currencyRate'] = 1;
					$arrAp['amountIDR'] = $total;

					$arrayToJs = $apCustomerCommission->addData($arrAp);  


					if($this->activeModule['activitylog']){ 
						$arrActivityLog = array();
						array_push($arrActivityLog, 
												array( 
														'modulekey' => 5, 
														'templatekey' => 18, 
														'refkey' => $hostuserkey,  
														'userkey' => $customerkey,  
														'amount' => $total,  
														'transkey' => $id  
													) 
										); 

						$activityLog = new ActivityLog();
						$activityLog->addNewLog($arrActivityLog);  
					}


					if (!$arrayToJs[0]['valid'])
						throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);

				}
				
		}

        //update jurnal umum 
        $this->updateGL($rsHeader);
         
    } 

	function isFirstSubscription($customerkey,$id){
		$sql = 'select pkey from '. $this->tableName.'
				where
					'. $this->tableName.'.pkey <> '.$this->oDbCon->paramString($id).' and
					'. $this->tableName.'.statuskey in (2,3) and
					'. $this->tableName.'.customerkey = '.$this->oDbCon->paramString($customerkey).' 
				limit 1
				';
		$rs = $this->oDbCon->doQuery($sql);
		  
		return (empty($rs)) ? true : false;
	}
	
	function getActiveSubscription($customerkey){
	 	$rsExistingSubscription = $this->searchDataRow(array($this->tableName.'.pkey',$this->tableName.'.trdate',$this->tableName.'.expdateuntil'),
								  ' and '.$this->tableName.'.customerkey =  ' .$this->oDbCon->paramString($customerkey).' 
									and '.$this->tableName.'.statuskey in (2,3)
									and '.$this->tableName.'.expdateuntil > date(now()) ',
								  ' order by '.$this->tableName.'.trdate desc, '.$this->tableName.'.pkey desc limit 1'
								  );
		
		return  $rsExistingSubscription;
	}
	
	function inLastSubscriptionPeriod($opt){
		 
		$dateNow = date('Y-m-d');
		$currMonth =  date('m');
		$currYear =  date('Y');
		 
		// ambil level skrg
		if(isset($opt['customerkey'])){
			$customer = new Customer();
			$customerkey = $opt['customerkey'];
			$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel',$customer->tableName.'.expdate'),
											' and '.$customer->tableName.'.pkey =  ' .$this->oDbCon->paramString($customerkey).'  
											')[0];
		}else{
			$rsCustomer = $opt['rs'];
		}
 
		//$this->setLog($rsCustomer,true);
		$expDateUntil = $rsCustomer['expdate'];
		$expDateUntilMonth = $this->formatDBDate($expDateUntil, 'm');
		$expDateUntilYear = $this->formatDBDate($expDateUntil, 'Y');
		
//		$this->setLog($currMonth .'=='. $expDateUntilMonth,true);
//		$this->setLog($currYear .'=='. $expDateUntilYear,true);
		
		// cek masih dalam periode subs gk
		$dateDiff = $this->dateDiff($this->formatDBDate($dateNow),$this->formatDBDate($expDateUntil));   
		 
		return ($dateDiff >= 0 && $currMonth == $expDateUntilMonth && $currYear == $expDateUntilYear) ? true : false;
	}

	function getAvailableSubscription($customerkey){
		
		// ambil level skrg
		
		$customer = new Customer();
		$membershipLevel = new MembershipLevel();
		
		$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel',$customer->tableName.'.expdate',$customer->tableName.'.currencypreference'),
											' and '.$customer->tableName.'.pkey =  ' .$this->oDbCon->paramString($customerkey).'  
											');

		$existingLevel = $rsCustomer[0]['membershiplevel']; 
		$expDateUntil = $rsCustomer[0]['expdate'];
		$currencykey = $rsCustomer[0]['currencypreference'];
		
		 // buat ambil level dan harga membership
		$rsMembership = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey',$membershipLevel->tableName.'.name',$membershipLevel->tableName.'.sellingprice',$membershipLevel->tableName.'.activeperiodmonth'),
												'and '.$membershipLevel->tableName.'.pkey > 1
												 and '.$membershipLevel->tableName.'.statuskey = 1 
												');
        
		$isLastPeriod = $this->inLastSubscriptionPeriod(array('rs'=>$rsCustomer[0]));
								 
		
		// loop setiap level
		// kalo level dibawah atau sama dengan saat ini, hanya muncul ketika diakhir periode
		// kalo level diatas muncul anytime, harga tergantung periode
		
		$arrReturn = array();
		
		foreach($rsMembership as $row){ 
			
			$newLevelKey = $row['pkey'];
            
			if(  ($row['pkey'] <= $existingLevel && $isLastPeriod) || $newLevelKey > $existingLevel ){
				
				$rsSubs = $this->calculateReSubscriptionTotal($customerkey, $newLevelKey, $currencykey); // sementara ambil ulang dulu, nanti kalo lemot baru dibenerin, harusnya kecil kemungkinan lemot karena cuma query customer
			 	$activePeriod = $rsSubs['totalMonth'] / 12;
				
				$yearInformation = ($activePeriod >= 1) ?   floor($activePeriod) .' Tahun': '';
				
				$description = (!$rsSubs['isNew'] ) ? 'Keanggotaan hingga ' . $this->formatDBDate($rsSubs['expDateUntil']) : '';
				
				array_push($arrReturn,
						   	array(
									'pkey' => $row['pkey'],
									'name' => $row['name'],
									'label' => $row['name']. ' ' .$yearInformation.' - Rp. '. $this->formatNumber($rsSubs['totalAmount']),
									'description' => $description ,
									'sellingPrice' => $rsSubs['totalAmount'], 
								 ) 
						  );
			} 
				
		}
		
//		$this->setLog('available subs ====',true);
//		$this->setLog($arrReturn,true);
		return $arrReturn;

	}
	
	function calculateReSubscriptionTotal ($customerkey,$levelkey,$currencykey){

		$membershipLevel = new MembershipLevel();
		$customer = new Customer();

		$dateNow = date('Y-m-d');
		 
		// ambil level skrg
		$rsCustomer = $customer->searchDataRow(array($customer->tableName.'.pkey',$customer->tableName.'.membershiplevel',$customer->tableName.'.expdate'),
											' and '.$customer->tableName.'.pkey =  ' .$this->oDbCon->paramString($customerkey).'  
											');

		$existingLevel = $rsCustomer[0]['membershiplevel']; 
		$expDateUntil = $rsCustomer[0]['expdate'];
		 
		 // buat ambil level dan harga membership
		$rsMembership = $membershipLevel->searchDataRow(array($membershipLevel->tableName.'.pkey',$membershipLevel->tableName.'.sellingprice',$membershipLevel->tableName.'.activeperiodmonth'),
												' and '.$membershipLevel->tableName.'.pkey =  ' .$this->oDbCon->paramString($levelkey).' 
												 and '.$membershipLevel->tableName.'.statuskey = 1 
												');
 
		if(empty($rsMembership)) die; // buat jaga2
		  
        $rsPrice = $membershipLevel->getMembershipPrice($levelkey,$currencykey);
		$sellingPrice =  $rsPrice[0]['sellingprice'];
		$activePeriod =  $rsMembership[0]['activeperiodmonth'];
		  
		// cek dulu  ada transaksi ga kalau ada update exp kalau gk ada add
		// kalo utk yg sudah expired 1 tahun lebih ??, berarti harus carinya ada gk yg subscriptionnya masih aktif hingga saat ini,
		// kalo gk ad, either blm pernah register atau sdh expired, anggap sebagai pendaftaran baru
		$rsExistingSubscription = $this->getActiveSubscription($customerkey);
		  
		// kalo blm pernah ad atau tidak ada sucscription yg aktif, return nilai normal
		// membership baru.
        
		if(empty($rsExistingSubscription)){
  
			return array( 
				'isNew' => true,
				'totalAmount' =>  $sellingPrice, 
				'totalMonth' => $activePeriod,
				'expDateUntil' => DEFAULT_EMPTY_DATE,
//				'upgradeDate' => $dateNow,
			);  
			
		}
			
		// kalo di bulan terakhir
		if( $this->inLastSubscriptionPeriod(array('rs'=>$rsCustomer[0]))){
			//$this->setLog("ppj di bln terakhir",true);
			
			// kalo sama / downgrade, perpanjang tetep harga satu periode saja
			
			// dan kalo level membershipnya yg baru lebih tinggi, tambah prorate bulan ini
			if($levelkey > $existingLevel)
				$sellingPrice += ( $sellingPrice / $activePeriod); // asumsi proratenya per bulan
			
			// exp date tambah 1 tahun
			
			$date = new DateTime($expDateUntil);  
			$date->add(new DateInterval('P'.$activePeriod.'M')); 
			$newExpDate = $date->format('Y-m-d');  
			
			$inLastPeriod = true;
			
		}else{
			// prorate
			// hanya berlaku utk upgrade
			$this->setLog("tambah validasi, hanya bisa utk upgrade",true);
			
			// cari selisih antara $expDateUntil dan skrg
			 
			$dateUpgrade  = new DateTime($dateNow); 
			$dateExp = new DateTime($expDateUntil); 
			//$resultDiff = (array)  date_diff($dateUpgrade, $dateExp);
			//$this->setLog($resultDiff,true);
				
			$diff  = $dateUpgrade->diff($dateExp); 
			$monthDiff = ($diff->y * 12) + $diff->m;
			
			$monthDiff += 1; // tambah bulan berjalan
			
			$sellingPrice = $monthDiff * ($sellingPrice / $activePeriod); // akan bermasalah kalo subscriptionnya diubah diatas 12 bln, buat membership baru kalo ad perubahan paket
			
			/// overwrite $activePeriod
			$activePeriod = $monthDiff;
			$newExpDate = $expDateUntil;
			
			$inLastPeriod = false;
		}
 
		return array( 
				'isNew' => false,
				'totalAmount' =>  $sellingPrice, 
				'totalMonth' => $activePeriod,
				'expDateUntil' =>  $newExpDate,
				'inLastPeriod' => $inLastPeriod
				);

 
	}
       
	function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
    
        //cek ad AR terbayar 
        $ar = new AR();
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and ('.$ar->tableName.'.statuskey  in (2,3))');
        if(!empty($rsAR)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['ar'][2]);
  
        $apCommission = new APCustomerCommission();
        $rsAPKey = $apCommission->getTableKeyAndObj($this->tableName,array('key')); 
        $rsAPCommission = $apCommission->searchDataRow(array('pkey'),
												   ' and '.$apCommission->tableName.'.refkey = '.$this->oDbCon->paramString($id).
												   ' and '.$apCommission->tableName.'.statuskey in (2,3)
                                                    and reftabletype = '.$this->oDbCon->paramString($rsAPKey['key'])
												 );
        if(!empty($rsAPCommission)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['apCommission'][2]);

    } 



    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey'];
        
		$ar = new AR();
		
		$refTableKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
        
        $rsARKey = $ar->getTableKeyAndObj($this->tableName); 
        $rsAR = $ar->searchData('','',true,' and reftabletype = '.$this->oDbCon->paramString($rsARKey['key']).' and refkey = '.$this->oDbCon->paramString($id).' and '.$ar->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsAR);$i++) {
            $arrayToJs = $ar->changeStatus($rsAR[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
  
     
        $apCommission = new APCustomerCommission();
		        
        $rsAPKey = $apCommission->getTableKeyAndObj($this->tableName,array('key')); 
        $rsAPCommission = $apCommission->searchDataRow(array('pkey'),
												   ' and '.$apCommission->tableName.'.refkey = '.$this->oDbCon->paramString($id).
												   ' and '.$apCommission->tableName.'.statuskey = 1
                                                    and reftabletype = '.$this->oDbCon->paramString($rsAPKey['key'])
												 );
        for($i=0;$i<count($rsAPCommission);$i++) {
            $arrayToJs = $apCommission->changeStatus($rsAPCommission[$i]['pkey'],TRANSACTION_STATUS['batal'],'',false,true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
		
		
		// harusnya sudah kehandle di afterStatusChanged
//		$sql = 'update '.$this->tableCustomer.' set membershiplevel = 1 where pkey = ' .$this->oDbCon->paramString($rsHeader[0]['customerkey']);
//		$this->oDbCon->execute($sql);
		
		if($rsHeader[0]['point'] > 0){
        	$rewardsPoint = new RewardsPoint();
			$rewardsPoint->cancelPointDeduction($rsHeader[0]['customerkey'], array('pkey' => $id, 'refTableType' => $refTableKey));
		}
		
        if ($copy)  $this->copyDataOnCancel($id);	  

		$activityLog = new ActivityLog();
		$activityLog->cancelLog($id);  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }  
 
	function  afterStatusChanged($rsHeader){
		
		// ambil ulang agar dpt status baru
		$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
		
		if ($rsHeader[0]['statuskey'] == 2 || $rsHeader[0]['statuskey'] == 4){ 
			$customer = new Customer();
			$customerFeatures = new CustomerFeatures();
			$membershiplevel = new MembershipLevel(); 

			$customer->updateExpDate($rsHeader[0]['customerkey']);
			
			 
			// update achivement   
			
			$rsMembershipLevel = $membershiplevel->searchDataRow(array($membershiplevel->tableName . '.defaulthostcommissionkey') , ' and ' . $membershiplevel->tableName . '.pkey=' . $this->oDbCon->paramString($rsHeader[0]['membershiplevelkey']));
			$rsCustomer = $customer->searchDataRow(array($customer->tableName . '.referralkey') , ' and ' . $customer->tableName . '.pkey=' . $this->oDbCon->paramString($rsHeader[0]['customerkey']));

			$defaultCustomerKey = $rsMembershipLevel[0]['defaulthostcommissionkey']; 
			$referralkey = (!empty($rsCustomer[0]['referralkey'])) ? $rsCustomer[0]['referralkey'] : $defaultCustomerKey; 
			$customerFeatures->updateMembershipAchievementsCounter($referralkey,'referral');


		}
		
	}
	
    function updateGL($rs){
		return; 
    }
     
	function afterUpdateData($arrParam, $action){  
        $pkey = $arrParam['pkey']; 
		
        // kalo add user baru 
	 	if($action == INSERT_DATA){ 
			
			$customer = new Customer();
			$currency = new Currency();
			
			// create paymentgateway invoice
			$rsHeader = $this->searchDataRow( array($this->tableName.'.code',$this->tableName.'.customerkey',$this->tableName.'.currencykey',$this->tableName.'.grandtotal'),
											  ' and '.$this->tableName.'.pkey = ' .  $this->oDbCon->paramString($pkey) 
						);
			$rsCustomer = $customer->searchDataRow( array($customer->tableName.'.code',$customer->tableName.'.name',$customer->tableName.'.email'),
											  ' and '.$customer->tableName.'.pkey = ' .  $this->oDbCon->paramString($rsHeader[0]['customerkey'])  
						);
				
         	$rsCurrency = $currency->searchDataRow( array($currency->tableName.'.pkey',$currency->tableName.'.code'),
											  ' and '.$currency->tableName.'.pkey = ' .  $this->oDbCon->paramString($rsHeader[0]['currencykey'])  
						);
				 
			$arrTransaction = array();
			$arrTransaction['code'] = $rsHeader[0]['code'];
			$arrTransaction['currency'] = (!empty($rsCurrency[0]['code'])) ? $rsCurrency[0]['code'] : 'IDR';
			$arrTransaction['amount'] = $rsHeader[0]['grandtotal'];
			$arrTransaction['customerEmail'] = $rsCustomer[0]['email'];
			$arrTransaction['description'] = $rsHeader[0]['code'];
 
            
			$response = $this->getPaymentGatewayInvoice($arrTransaction);
			
			if(!empty($response)){
				$sql = 'update '.$this->tableName.' set paymentgatewayinvoiceurl = '. $this->oDbCon->paramString($response['invoice_url']) .' where pkey = ' . $this->oDbCon->paramString($pkey) ;
				$this->oDbCon->execute($sql);
			}
			
			// harus dibawah, karena perlu paymentgatewayinvoiceurl
//        	$this->setLog("sementara dikomen, nanti dibenerin pas update lang",true);
            $this->sendSubscriptionInvoiceEmail($pkey);
		}
    } 
    
	    
	function sendSubscriptionInvoiceEmail($pkey){
		
        global $twig;
		
		
		$rs = $this->searchData($this->tableName.'.pkey',$pkey );
		
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rs[0]['customername'];
        $arrTwigVar['MEMBERSHIP_NAME'] = $rs[0]['membershiplevel'];
        $arrTwigVar['MEMBERSHIP_PRICE'] = $rs[0]['grandtotal'];
        $arrTwigVar['PAYMENT_LINK'] = $rs[0]['paymentgatewayinvoiceurl'];
        $arrTwigVar['CURRENCY_CODE'] = $rs[0]['currencycode'];
        
		// kalo file nya ada baru dikirim   

        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
            ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rs[0]['customerlangkey'])
          );

        $filePath = $this->getLangTemplatePath('email-subscription-invoice.html',true,$rsLang[0]['code']);
       
		if (file_exists($filePath)){ 
            $content = $twig->render($filePath, $arrTwigVar);
			$this->sendMail(array(), $this->lang['membership'] . ' - ' . DOMAIN_NAME,$content,array('name' => $rs[0]['customername'], 'email'=>$rs[0]['customeremail'])); 
		}
         
		// kirim WA
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-subscription-invoice.html',true,$rsLang[0]['code']), $arrTwigVar); 
			$content = html_entity_decode(strip_tags($content));
            
            if(!empty($rs[0]['customermobilecode'])) $rs[0]['customermobile'] = $rs[0]['customermobilecode'] . $rs[0]['customermobile'];
			$this->sendWA($rs[0]['customermobile'],$content,true);
		}
	}
	 
	function sendReminderDaysEmail($rsCustomer,$days){
		 
        global $twig;
		
		$membershiplevel = new MembershipLevel();
		
		$rsMembershipLevel = $membershiplevel->searchDataRow(array($membershiplevel->tableName . '.pkey',$membershiplevel->tableName . '.name'),
															' and '.$membershiplevel->tableName . '.statuskey = 1' 
															); 
				
		$rsMembershipLevel = array_column($rsMembershipLevel,null,'pkey');
		
		$sendWA  = (!empty($this->loadSetting('WAGatewayAPIKey'))) ? true : false;
		
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $lang = new Lang();
        
		foreach($rsCustomer as $customerRow){
			$arrTwigVar['CUSTOMER_NAME'] = $customerRow['name']; 
			$arrTwigVar['MEMBERSHIP_LEVEL'] = $rsMembershipLevel[$customerRow['membershiplevel']]['name'];
 
            $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                            ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($customerRow['langkey'])
                                          );
             
			// kalo file nya ada baru dikirim 
			$content = $twig->render( $this->getLangTemplatePath('email-'.$days.'.html',true,$rsLang[0]['code']), $arrTwigVar);  
		
			$this->sendMail(array(), DOMAIN_NAME,$content,array('name' => $customerRow['name'], 'email'=> $customerRow['email'])); 

			// kirim WA
			if($sendWA){ 
				$content = $twig->render( $this->getLangTemplatePath('wa-'.$days.'.html',true,$rsLang[0]['code']) , $arrTwigVar); 
				$content = html_entity_decode(strip_tags($content));
				  
                if(!empty($customerRow['mobilecode'])) $customerRow['mobile'] = $customerRow['mobilecode'] . $customerRow['mobile'];
				$this->sendWA($customerRow['mobile'],$content,true);
			}
             
		}
		
     
	}
	
    
    function sendReminderWillExpiredEmail($rsCustomer,$days){
		 
        global $twig;
				
        // nanti jadikan default variable
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $lang = new Lang();
        
		$sendWA  = (!empty($this->loadSetting('WAGatewayAPIKey'))) ? true : false;
        
		foreach($rsCustomer as $customerRow){
			$arrTwigVar['CUSTOMER_NAME'] = $customerRow['name'];  
			$arrTwigVar['EXPIRED_TIME'] = abs($days);
			
            $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                            ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($customerRow['langkey'])
                                          );
            
            $arrTwigVar['EXPIRED_DATE'] = $this->toLocalDate($this->formatDBDate($customerRow['expdate'],'d F Y'),$rsLang[0]['code']);

            if($days == 0){
                $templateHtml = 'email-expired';
                $waTemplate = 'wa-expired';
            }else if($days < 0){
                $templateHtml = 'email-already-expired'; 
                $waTemplate = 'wa-already-expired';
            }else{ 
                $templateHtml = 'email-will-expired';
                $waTemplate = 'wa-will-expired';
            }
             
			// kalo file nya ada baru dikirim 
			$content = $twig->render( $this->getLangTemplatePath($templateHtml.'.html',true,$rsLang[0]['code']), $arrTwigVar);  
            
            // sementara overwrite utk test
            
            $this->setLog($customerRow['name'],'true','exp-member.txt');
//            $this->setLog($content,'true','exp-member.txt');
 
//			$this->sendMail(array(), DOMAIN_NAME,$content,array('name' => $customerRow['name'], 'email'=> $customerRow['email']));
			$this->sendMail(array(), DOMAIN_NAME,$content,array('name' => $customerRow['name'], 'email'=> 'martinhalimk@gmail.com'));
            $this->sendMail(array(), DOMAIN_NAME,$content,array('name' => $customerRow['name'], 'email'=> 'juanda.rovelim@gmail.com'));
             
            
			// kirim WA
			if($sendWA){ 
				$content = $twig->render( $this->getLangTemplatePath($waTemplate.'.html',true,$rsLang[0]['code']) , $arrTwigVar); 
				$content = html_entity_decode(strip_tags($content));
				  
                if(!empty($customerRow['mobilecode'])) $customerRow['mobile'] = $customerRow['mobilecode'] . $customerRow['mobile'];
//				$this->sendWA($customerRow['mobile'],$content,true);
                
                $this->sendWA('62818110284',$content,true); 
			}
		} 
     
	}
    
    function generateInvoice($pkey){   
        $rsHeader = $this->getDataRowById($pkey);

        $file=  HTTP_HOST . 'membership-invoice/'.$pkey.'/'.md5($pkey . $rsHeader[0]['grandtotal'] . $this->secretKey).'/1';     
        $invoice =  file_get_contents($file);
        
        return $invoice;
    }

	
	function getMonthlySalesSummary($startPeriod = '',$endPeriod ='',  $criteria='',$groupby = ''){
        
        // DATE FORMAT => d / m / Y

        if (empty($startPeriod)) $startPeriod = DEFAULT_EMPTY_DATE; 
        if (empty($endPeriod)) $endPeriod = date('d / m / Y');
         
        
        // be aware, perubahan group harus update ke concat index jg
        if (empty($groupby))
            $groupby = 'membershiplevelkey, year(trdate), month(trdate)';
        
        $sql  = '
                select 
                    '.$this->tableMembershipLevel.'.name, 
                    membershiplevelkey,
                    concat(membershiplevelkey,\'-\',DATE_FORMAT(trdate, \'%c%Y\'))  as periodindex,
                    month(trdate) as month,   
                    year(trdate) as year, 
                    sum('.$this->tableName.'.grandtotal) as grandtotal,
					count('.$this->tableName.'.pkey) as qty
                from 
                    '.$this->tableMembershipLevel.',
                    '.$this->tableName.'
                where 
                    '.$this->tableMembershipLevel.'.pkey = '.$this->tableName.'.membershiplevelkey';
                   
         $sql .= ' and  '.$this->tableName.'.trdate between '. $this->oDbCon->paramDate($startPeriod.' 00:00:00',' / ') .' and LAST_DAY('. $this->oDbCon->paramDate($endPeriod.' 23:59:59',' / ') .')';
         $sql .= ' and   '.$this->tableName.'.statuskey in (2,3)';
         $sql .=  $this->getWarehouseCriteria() ;
        
        if (!empty($criteria))  $sql .= ' ' .$criteria;
        
        $sql .=' group by ' .$groupby;
         
        $rs = $this->oDbCon->doQuery($sql);
        
        return $rs;
    }
    
	
     function normalizeParameter($arrParam, $trim = false){
 
			$termOfPayment = new TermOfPayment();
            $customer = new Customer();
		 	//$membershipLevel = new MembershipLevel();
		 
		 	$arrParam['checksum'] = md5(time().$this->generateStrongPassword()); 
			$arrParam['paymentMethodValue'] = (isset($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();
 
			//$arrVoucherkey = $arrParam['hidVoucherKey']; 
		 
			$rsTOP = $termOfPayment->getDataRowById($arrParam['selTermOfPaymentKey']);  
			if ($rsTOP[0]['duedays'] != 0){   
				for($i=0;$i<count( $arrParam['paymentMethodValue']);$i++){ 
					$arrParam['paymentMethodValue'][$i] = 0; 
					$arrParam['hidDetailPaymentKey'][$i] = 0;
				}
			}
         
            $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.currencypreference'),
											' and '.$customer->tableName.'.pkey =  ' .$this->oDbCon->paramString($arrParam['hidCustomerKey']).'  
											');

            $arrParam['hidCurrencyKey'] = $rsCustomer[0]['currencypreference'];
   
            $reCountResult = $this->reCountSubtotal($arrParam);
			 
            $arrParam['detailVoucher'] = $reCountResult['detailVoucher'];
            $arrParam['subtotal'] = $reCountResult['subtotal'];
		 	$arrParam['activePeriod'] = $reCountResult['activePeriod'];
		  
            $arrParam['pointValue'] = $reCountResult['pointValue'];
            $arrParam['beforeTaxTotal'] = $reCountResult['beforeTaxTotal'];
            $arrParam['isPriceIncludeTax'] = $reCountResult['isPriceIncludeTax'];
            $arrParam['taxValue'] = $reCountResult['taxValue'];
            $arrParam['grandtotal'] = $reCountResult['grandtotal'];
            $arrParam['totalPayment'] = $reCountResult['totalPayment'];
            $arrParam['balance'] = $reCountResult['balance']; 
            
		 	 
        $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam;
    }
     
    function updateMidtransResponse($response){
        
        if($this->isJSON($response))
            $postVars = json_decode($response,true);
        else
            parse_str($response,$postVars);  
        
        $transCode = explode('-',$postVars['order_id']);  
        array_pop($transCode); 
        $transCode = implode('',$transCode); 
        
        $transactionStatus = $postVars['transaction_status'];
        $amount =  $postVars['gross_amount'];
        
         try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $sql = 'update ' .$this->tableName.' set paymentgatewayresponse =  \''.addslashes(json_encode($postVars)).'\'
                        where code = ' . $this->oDbCon->paramString($transCode) ; 
                $this->oDbCon->execute($sql); 

                $this->oDbCon->endTrans();

        }catch (Exception $e){
            $this->oDbCon->rollback();
             
        } 
        
    }
    
    function updatePaymentGatewwayResponse($pkey,$arr){
          try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $sql = 'update '.$this->tableName.' 
                        set 
                            paymentgatewayinvoiceurl = '.$this->oDbCon->paramString($arr['invoiceURL']) .' 
                        where 
                            pkey = ' . $this->oDbCon->paramString($pkey) ;
             
                $this->oDbCon->execute($sql); 

                $this->oDbCon->endTrans();

        }catch (Exception $e){
            $this->oDbCon->rollback();
             
        } 
    }
    
    function sendPaymentInstruction($pkey){
        global $twig;
        
        $sql = 'select
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.paymentgatewayinvoiceurl, 
                    '.$this->tableCustomer.'.name,
                    '.$this->tableCustomer.'.email
                from 
                    '.$this->tableName.'
                    left join '.$this->tableCustomer.' on  '.$this->tableName.'.customerkey =  '.$this->tableCustomer.'.pkey
                where
                    '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey);
        
        
        $rs = $this->oDbCon->doQuery($sql);

        $transactionCode = $rs[0]['code'];
        $email = $rs[0]['email']; // (!empty($rs[0]['email'])) ? $rs[0]['email'] : $rs[0]['recipientemail']; // kalo pake email satu lg, nanti gk konsisten dengna email di shopping cart
        $name =  $rs[0]['name']; //(!empty($rs[0]['name'])) ? $rs[0]['name'] : $rs[0]['recipientname'];
        
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $name;
        $arrTwigVar['INVOICE_URL'] = $rs[0]['paymentgatewayinvoiceurl'];
        
        $twig->render('email-template.html');  
        $content = $twig->render('email-payment-instruction.html', $arrTwigVar);

        /*$this->setLog($this->lang['paymentInstruction'] . ' ' .$transactionCode.' - ' . DOMAIN_NAME);
        $this->setLog($email);*/
        
        $this->sendMail(array(), $this->lang['paymentInstruction'] . ' ' .$transactionCode.' - ' . DOMAIN_NAME,$content,array('email'=>$email)); 
 
    }
    
    function paymentGatewaySuccess($pkey){
        try{  
                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);

                    $arrayToJs = array(); 


                    $termOfPayment = new TermOfPayment();
                    $rsTOP = $termOfPayment->searchDataRow(array($termOfPayment->tableName.'.pkey'),
                                                            ' and '.$termOfPayment->tableName.'.duedays > 0 ',
                                                            ' order by duedays asc limit 1'
                                                          );
            
                    $topkey = (!empty($rsTOP)) ? $rsTOP[0]['pkey'] : 1;
            
                    $sql = 'update ' .$this->tableName.' 
                            set paymentgatewaysuccess = 1, termofpaymentkey = '.$this->oDbCon->paramString($topkey).' 
                            where  
                            pkey = ' .  $this->oDbCon->paramString($pkey) ;
            
                    $this->oDbCon->execute($sql); 

                    $this->oDbCon->endTrans();

                    $this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

        }catch (Exception $e){
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs,false,$e->getMessage());    
        }
    }
	
	
	function getPendingSubscription($userkey,$limit=1){
		$limit = (int) $limit;
		
		return $this->searchDataRow(array( $this->tableName.'.pkey', $this->tableName.'.code', $this->tableName.'.checksum' ),
								' and '.$this->tableName.'.customerkey =  '.$this->oDbCon->paramString($userkey).' 
								  and '.$this->tableName.'.statuskey = 1',
								' order by '.$this->tableName.'.createdon desc limit ' . $limit
							);  
	}
	
	function getLatestUpgradeCustomer($membershiplevelkey = array()){
		$sql  = '
					select 
						'.$this->tableCustomer.'.pkey,
						'.$this->tableCustomer.'.code,
						'.$this->tableCustomer.'.name,
						'.$this->tableCustomer.'.email,
						'.$this->tableCustomer.'.mobile,
						'.$this->tableCustomer.'.photofile,
						'.$this->tableCustomer.'.companyname,
						'.$this->tableCustomer.'.membershiplevel,
						'.$this->tableCustomer.'.photofile,
						'.$this->tableJobPosition.'.name as jobpositionname,
                    	'.$this->tableBusinessCategory . '.name as mainbusinessname,
						'.$this->tableMembershipLevel.'.name as membershiplevelname
					from	
						'.$this->tableName.',
						'.$this->tableCustomer.'
							left join ' . $this->tableBusinessCategory . ' on ' . $this->tableCustomer . '.mainbusinesskey = ' . $this->tableBusinessCategory . '.pkey  
						 	left join '.$this->tableJobPosition.' on '.$this->tableCustomer.'.jobpositionkey = '.$this->tableJobPosition.'.pkey
						 	left join '.$this->tableMembershipLevel.' on '.$this->tableCustomer.'.membershiplevel = '.$this->tableMembershipLevel.'.pkey
					where 
						'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey ';
		
		if(!empty($membershiplevelkey))
				$sql .= ' and '.$this->tableName.'.membershiplevelkey in('. $this->oDbCon->paramString($membershiplevelkey,',').')'; 
				
		$sql .= 'order by '.$this->tableName.'.confirmedon desc limit 5';
		
		return $this->oDbCon->doQuery($sql);
	}
	
	function getNewCustomerReferred($customerkey,$arrMembershipLevel = array()){
		
		// khusus PRO
		// asumsi bulan berjalan
		$datePeriod = date('01 / m / Y');
		
		// query semua customer yg approve subs di tahun ini
		// patokan sementara dr tgl transaksi diakui. kalo dr tgl konfirmasi nanti aka nrepot kalo ad cancel dan approve ulang
		
		$criteria =  ' and '.$this->tableName.'.membershiplevelkey in ('.$this->oDbCon->paramString($arrMembershipLevel,',').')
											   and '.$this->tableName.'.statuskey in (2,3)
											   and '.$this->tableCustomer.'.referralkey = '.$this->oDbCon->paramString($customerkey);
		
		
		$rsSubs = $this->searchData( '', '', true, $criteria. ' and year('.$this->tableName.'.trdate) = ' .$this->oDbCon->paramDate($datePeriod,' / ','Y') );
		$rsSubsCustomerKey = array_unique(array_column($rsSubs,'customerkey'));
		
		// exclude dengan yg sudah pernah pro ?		
		$rsExclude = $this->searchData( '', '', true, $criteria. ' and year('.$this->tableName.'.trdate) < ' .$this->oDbCon->paramDate($datePeriod,' / ','Y') );
		$rsExcludeCustomerKey = array_unique(array_column($rsExclude,'customerkey'));
		
		$rsNew = array_diff($rsSubsCustomerKey,$rsExcludeCustomerKey);
		
		return $rsNew;
		   
	}
	  
//	
//	  
//    function getVoucherDetail($pkey,$paymentMethodKey=''){
//        $paymentMethodKeyCriteria = '';
//        if (!empty($paymentMethodKey))
//            $paymentMethodKeyCriteria = ' and  '.$this->tableVoucherDetail.'.voucherkey = ' . $this->oDbCon->paramString($paymentMethodKey);
//
//        $sql = 'select 
//                    '.$this->tableVoucherDetail.'.*,
//					'.$this->tableVoucherTransaction.'.code
//                from  
//                   '.$this->tableVoucherDetail.',
//				   '.$this->tableVoucherTransaction.'
//                where  
//					'.$this->tableVoucherDetail.'.voucherkey =  '.$this->tableVoucherTransaction.'.pkey and
//                    '.$this->tableVoucherDetail.'.refkey in ('.$this->oDbCon->paramString($pkey,',').')
//					'.$paymentMethodKeyCriteria.'
//				order by 
//					 '.$this->tableVoucherDetail.'.voucherkey asc';	
//        return $this->oDbCon->doQuery($sql); 
//    } 
//       
//    
//     
    
        
}
?>
