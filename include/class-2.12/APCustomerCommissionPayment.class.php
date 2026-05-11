<?php

class APCustomerCommissionPayment extends BaseClass{  
  
    function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ap_customer_commission_payment_header';
		$this->tableNameDetail = 'ap_customer_commission_payment_detail';
		$this->tableCustomer = 'customer';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tablePayment= 'ap_customer_commission_payment';
		$this->tableCost= 'ap_customer_commission_cost';
        $this->tableItem = 'cost_cash_out';
		
        $this->tableDownpaymentDetail = 'ap_customer_commission_downpayment';// harusnya gk aka npernah kepake
        $this->tableDownpayment = 'customer_downpayment'; // harusnya gk aka npernah kepake
	    $this->tableAP = 'ap';
		$this->tableAPType = 'ap_type';
        $this->tablePaymentMethod = 'payment_method';

        $this->isTransaction = true;
        
		$this->securityObject = 'APCustomerCommissionPayment';
        
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['apkey'] = array('hidAPKey');
        $this->arrDataDetail['outstanding'] = array('outstanding','number');
        $this->arrDataDetail['amount'] = array('amount', array('datatype' => 'number','mandatory'=>true));
        $this->arrDataDetail['discount'] = array('discount','number');
        $this->arrDataDetail['taxamount'] = array('taxPPH','number');
       
        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 
        
  	    $arrCostDetail = array(); 
        $arrCostDetail['pkey'] = array('hidDetailCostKey');
        $arrCostDetail['refkey'] = array('pkey', 'ref');
        $arrCostDetail['amount'] = array('costAmount',array('datatype' => 'number','mandatory'=>true));
        $arrCostDetail['costkey'] = array('hidCostKey',array('mandatory'=>true)); 

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment)); 
        array_push($arrDetails, array('dataset' => $arrCostDetail, 'tableName' => $this->tableCost));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');	
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trnotes'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['grandtotal'] = array('grandtotal','number');
        $this->arrData['totalpayment'] = array('totalPayment','number');
        $this->arrData['totaldownpayment'] = array('totalDownpayment','number');
        $this->arrData['totalcost'] = array('totalCost','number');
        $this->arrData['totaldiscount'] = array('totalDiscount','number');
        $this->arrData['balance'] = array('balance','number');
        $this->arrData['payabletax23'] = array('pph23','number'); 
        $this->arrData['totalpaid'] = array('totalPaid','number');
        $this->arrData['usedateperiod'] = array('chkDatePeriod');
        $this->arrData['startdateperiod'] = array('trStartDate','date');
        $this->arrData['enddateperiod'] = array('trEndDate','date');
              
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'totalpaid', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'tax23','title' => 'tax23','dbfield' => 'payabletax23', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
    
            
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/apCustomerCommissionPayment'));
        
        $this->includeClassDependencies(array(  
                  'AP.class.php',
                  'APCustomerCommission.class.php',
                  'Downpayment.class.php'  ,
                  'CustomerDownpayment.class.php' ,
                  'GeneralJournal.class.php' ,
                  'COALink.class.php',
                  'APPayableTax23.class.php',
                  'Service.class.php'
        ));
        
        $this->overwriteConfig(); 
	}
	
    function getQuery(){
		 
			$sql = '
				SELECT '.$this->tableName.'.* ,
				   '.$this->tableCustomer.'.name as customername,
				   '.$this->tableCustomer.'.bankname as customerbankname,
				   '.$this->tableCustomer.'.bankaccountname as customeraccountname,
				   '.$this->tableCustomer.'.bankaccountnumber as customeraccountno,
				   '.$this->tableWarehouse.'.name as warehousename, 
				   '.$this->tableStatus.'.status as statusname
				FROM 
					'.$this->tableName.'  
						left join '.$this->tableCustomer.' on  '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey , 
					'.$this->tableStatus.',  
					'.$this->tableWarehouse.'
				WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
			';
		
	   $sql .= $this->criteria;
		
        return $sql;
	}
     
 
	function reCountGrandtotal($arrParam){

				$grandtotal = 0;
				$amount = 0;
                $discount = 0;
                $pph = 0;
				
				$arrAPkey = $arrParam['hidAPKey'];
				$arrAmount = $arrParam['amount'];
				$arrDiscount = $arrParam['discount'];
                $arrPph = $arrParam['taxPPH'];
				//$arrPick = $arrParam['chkPick']; 
				
				$arrAPDetail = array(); 
				
				for ($i=0;$i<count($arrAPkey);$i++){
					
				    $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
					if ( empty($arrAPkey[$i]) || empty($arrAmount[$i]) )  // || empty($arrPick[$i])
						continue;
                    
				    $amount += $this->unFormatNumber($arrAmount[$i]);
                   
				    if (!empty($arrPph[$i]) && $arrPph[$i]>0)  
						$pph += $this->unFormatNumber($arrPph[$i]);
                    if (!empty($arrDiscount[$i]) && $arrDiscount[$i]>0)  
						$discount += $this->unFormatNumber($arrDiscount[$i]);
				} 


                $totalpaid = $amount + $discount;
                $totalDowpayment = 0; 
				$downpayment = $arrParam['downpaymentAmount'];
				$downpaymentKey = $arrParam['hidDownpaymentKey'];
				for($i=0;$i<count($downpayment);$i++){
                    if(empty($downpaymentKey[$i]))
                        continue;
					$totalDowpayment += $this->unFormatNumber($downpayment[$i]);
				}
				
               // $item = new Item();
                $totalCost = 0; 
				$costAmount = $arrParam['costAmount'];
				$costKey = $arrParam['hidCostKey'];
				for($i=0;$i<count($costAmount);$i++){
//                    $rsItem = $item->getDataRowById($costKey[$i]); // harusnya gk perlu
//                    if(empty($rsItem))  continue;
                    
					$totalCost += $this->unFormatNumber($costAmount[$i]);
				}
				 
				$grandtotal = $amount - $pph - $totalDowpayment + $totalCost ;
				$balance = 0;
				$totalPayment = 0; 
				$payment = $arrParam['paymentMethodValue'];
				for($i=0;$i<count($payment);$i++){
					$totalPayment += $this->unFormatNumber($payment[$i]);
				} 
				$balance = $totalPayment  - $grandtotal;

				$reCountResult = array();
				$reCountResult['totalPaid'] = $totalpaid;
				$reCountResult['totalDiscount'] = $discount;
				$reCountResult['pph23'] = $pph;
				$reCountResult['totalPayment'] = $totalPayment;
                $reCountResult['totalDownpayment'] = $totalDowpayment;
				$reCountResult['grandtotal'] = $grandtotal;
				$reCountResult['balance'] = $balance;
                $reCountResult['totalCost'] = $totalCost;
               
				return $reCountResult;
               
				
	}
 
	
	function validateForm($arr,$pkey = ''){
        
		$APObj = $this->getAPObj();
        //$downpayment = new SupplierDownpayment();
        //$truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$customerkey = $arr['hidCustomerKey'];  
		$arrAPkey = $arr['hidAPKey']; 
		$arrAmount = $arr['amount'];
		$arrOutstanding= $arr['outstanding'];
		$arrDiscount = $arr['discount'];
        $arrDownpaymentKey = $arr['hidDownpaymentKey'];
		$arrDownpaymentAmount = $arr['downpaymentAmount'];
		$arrDownpaymentCode = $arr['downpaymentCode'];
		$refkey = $arr['hidRefKey'];
        $trDate = $arr['trDate'];
		//$arrPick = $arr['chkPick']; 

        
        $arrDetailKey = array(); 
        
        $arrAP = array();
        $rsAP = $APObj->searchData('','',true, ' and '.$APObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrAPkey)).') '); 
        $arrAP = array_column($rsAP, 'code', 'pkey');
        $arrAPCustomer = array_column($rsAP, 'customerkey', 'pkey');
        $arrAPRefkey2 = array_column($rsAP, 'refkey2', 'pkey');
        $arrDate = array_column($rsAP, 'trdate', 'pkey');
         
        //khusus trucking
//        $refCode = '';
//        if(!empty($refkey)) {
//            $rsWO =  $truckingServiceWorkOrder->getDataRowById($refkey);
//            $refCode = $rsWO[0]['code'];
//        } 
        
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
		   
        $hasAP = false; 
		for($i=0;$i<count($arrAPkey);$i++) { 
			if (!empty($arrAPkey[$i]))  //  && !empty($arrPick[$i])
                $hasAP = true;  
            
            if (in_array($arrAPkey[$i],$arrDetailKey)){   
                $this->addErrorList($arrayToJs,false, $arrAP[$arrAPkey[$i]].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKey, $arrAPkey[$i]);
            }
            
        }
        
        if (!$hasAP)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ap'][1]); 	
        
		for($i=0;$i<count($arrAPkey);$i++) {  
            if(!empty($arrAPkey[$i])){
                
                if ( $this->unFormatNumber($arrAmount[$i]) <= 0) 
                    $this->addErrorList($arrayToJs,false,$arrAP[$arrAPkey[$i]]. '. ' . $this->errorMsg['apPayment'][2]); 
                
                if ($arrAPCustomer[$arrAPkey[$i]] <> $customerkey) 
                    $this->addErrorList($arrayToJs,false,$arrAP[$arrAPkey[$i]]. '. ' . $this->errorMsg['ap'][5]); 
                
                //validasi kalo isi JO sebagai referensi, pastikan semua hutangnya mengacu ke SPK dengan JO yg sama
                //if(!empty($refkey) && $arrAPRefkey2[$arrAPkey[$i]] <> $refkey){
                    //$this->addErrorList($arrayToJs,false,$arrAP[$arrAPkey[$i]]. '. ' . $this->errorMsg['ap'][7] .' ('.$refCode.')'); 
                //}
                
                $apDate = $this->formatDBDate($arrDate[$arrAPkey[$i]],'d / m / Y');
                $dateDiff = $this->dateDiff($trDate,$apDate);
                
                if($dateDiff > 0)
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]].'</strong>.'. $this->errorMsg['apPayment'][4]);
            } 
		}
 
        
       /* $arrDownpaymentExistKey = array(); 
		for($i=0;$i<count($arrDownpaymentKey);$i++) {  
            if(empty($arrDownpaymentKey[$i]))
                continue;
            
            // validasi DP masi available gk
            $rsDP = $downpayment->searchData($downpayment->tableName.'.pkey',$arrDownpaymentKey[$i],true, ' and '.$downpayment->tableName.'.statuskey in (2) ');
            if(empty($rsDP)){ 
                $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][9]);
            }else{
                if ($supplierkey <> $rsDP[0]['supplierkey'])
                    $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][6]); 

                // validasi nilai DP masi mencukupi gk
                $amount = $this->unformatNumber($arrDownpaymentAmount[$i]);
                if ($amount > $rsDP[0]['outstanding'] )
                    $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDP[0]['outstanding']).')');  
            }
            
            
            // cek double gk
             if (in_array($arrDownpaymentKey[$i],$arrDownpaymentExistKey)){  
                $this->addErrorList($arrayToJs,false, $rsDP[0]['code'].'. '.$this->errorMsg[215]); 	 
            }else{ 
                if (!empty($arrDownpaymentKey[$i])) {  
                    array_push($arrDownpaymentExistKey, $arrDownpaymentKey[$i]);
                }
            }

                
        }*/
				
        return $arrayToJs;
	 }
    
    function afterStatusChanged($rsHeader){ 
        
		$APObj = $this->getAPObj();
         
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
  
        for($i=0;$i<count($rsDetail); $i++){   
           $APObj->updateAPCommissionOutstanding($rsDetail[$i]['apkey']); 
        }   
           
//        $supplierDownpayment = new SupplierDownpayment();
//        $rsDownpayment = $this->getDownpaymentDetail($rsHeader[0]['pkey'],'',false);
//        for($i=0;$i<count($rsDownpayment); $i++){  
//           $supplierDownpayment->updateOutstanding($rsDownpayment[$i]['downpaymentkey']); 
//        }
        
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
	  
	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $customerkey =  $rsHeader[0]['customerkey'];
        
		$coaLink = new COALink();
        $warehouse = new Warehouse();  
        $ap = $this->getAPObj();
         
        $rsPayment = $this->getPaymentMethodDetail($id); 
        //$rsDownpayment = $this->getDownpaymentDetail($id,'',false);
        
        $totalPayment = 0; 
        for($i=0;$i<count($rsPayment); $i++)
            $totalPayment += $rsPayment[$i]['amount'];
        

        $balance = $totalPayment - $rsHeader[0]['grandtotal']; 
          
        $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
        if($balance < ($thresholdDiscount * -1)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
        else if ($balance > $thresholdDiscount)
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 


        if (USE_GL){  
            $arrCOA = array();
            array_push($arrCOA, 'ap' , 'otherrevenue', 'othercost','payabletax23'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }    
            
            for($i=0;$i<count($rsPayment); $i++){ 
                if ($rsPayment[$i]['amount'] > 0 ){ 
                    $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg['coa'][3]);
                }
            }    
        }  
        
        $rsDetail = $this->getDetailById($id);
        $arrKeys = array_column($rsDetail,'apkey');        
        // cek status hutang sudah lunas atau blm
        if (!empty($arrKeys)){
            $arrKeys = implode(',',$arrKeys);
            $rsAP = $ap->searchData('','',true,' and ' .$ap->tableName.'.pkey in ('.$arrKeys.') and ' .$ap->tableName.'.statuskey in (3,4) ' );
            if (!empty($rsAP)){
                $arrAP = array_column($rsAP,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br>'.implode(', ',$arrAP).'. '.$this->errorMsg['ap'][6]); 
            }
        }
 
//        for($i=0;$i<count($rsDownpayment);$i++) {   
//            
//            // validasi DP masi available gk 
//            if($rsDownpayment[$i]['downpaymentstatuskey'] <> 2){ 
//                $this->addErrorList($arrayToJs,false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][9]);
//            }else{
//                //if($customerkey <> $rsDownpayment[$i]['downpaymentsupplierkey'])
//                    //$this->addErrorList($arrayToJs,false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][7]); 
//
//                // validasi nilai DP masi mencukupi gk 
//                if ($rsDownpayment[$i]['amount'] > $rsDownpayment[$i]['downpaymentoutstanding'] )
//                    $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDownpayment[$i]['downpaymentoutstanding']['outstanding']).')');  
//            }
//                
//        }
       
        $rsAP = $ap->searchData('','',true,' and ' .$ap->tableName.'.pkey in ('.$arrKeys.') ' );
        $trDate =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        for($i=0;$i<count($rsAP);$i++){
            $apDate = $this->formatDBDate($rsAP[$i]['trdate'],'d / m / Y');
            $dateDiff = $this->dateDiff($trDate,$apDate);
            if($dateDiff > 0)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['apPayment'][4]); 
        }
 	 }
    
	 
	function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
         
        $coaLink = new COALink(); 
		$warehouse = new Warehouse();  
		$customer = $this->getBussinessPartnerObj();
		//$cashMovement = new CashMovement();  
        
		$rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
		$notecash = $rsHeader[0]['code'].'. Kas Keluar untuk pembayaran komisi '.$rsCustomer[0]['name'];
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
		 
		// MENGHITUNG PAYMENT
		$rsPayment = $this->getPaymentMethodDetail($id);   
		
		for($i=0;$i<count($rsPayment); $i++){   
            if (USE_GL) {
               $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
               $coakey = $rsPaymentCOA[0]['coakey']; 
           }else{
               $coakey = $rsPayment[$i]['paymentkey'];
           }

           //$cashMovement->updateCashMovement($id, $coakey,-$rsPayment[$i]['amount'], $this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
		}           
		
		// END
		
		//update jurnal umum 
        $this->updateGL($rsHeader);
        
//        if ($rsHeader[0]['payabletax23'] != 0) 
//            $this->updateAPPrepaid($rsHeader,$rsDetail); 
		
		
		$this->sendPaymentEmail($id);
	} 
     
	
	function sendPaymentEmail($pkey){
		require_once  $_SERVER ['DOCUMENT_ROOT'].'/_include-twig.php';
       
		$customer = new Customer();
		
		// kirim email 
        $rsAPPayment = $this->getDataRowById($pkey);
		$rsPaymentMethod = $this->getPaymentMethodDetail($pkey);
		$paymentMethodName = implode(', ',array_column($rsPaymentMethod,'paymentmethodname'));
		 
		$rsCust = $customer->getDataRowById($rsAPPayment[0]['customerkey'] );
		
        $arrTwigVar = array();
        $arrTwigVar = $this->getDefaultEmailVariable();
         
        $arrTwigVar['CUSTOMER_NAME'] = $rsCust[0]['name']; 
		$arrTwigVar['AMOUNT'] = $rsAPPayment[0]['totalpaid'];
		$arrTwigVar['TAX_AMOUNT'] = $rsAPPayment[0]['payabletax23'];
		$arrTwigVar['TOTAL_PAID'] = $rsAPPayment[0]['totalpayment'];
		$arrTwigVar['PAYMENT_METHOD'] = $paymentMethodName;

        $lang = new Lang();
        $rsLang = $lang->searchDataRow(array($lang->tableName.'.code'),
                                ' and '.$lang->tableName.'.pkey = '.$this->oDbCon->paramString($rsCust[0]['langkey'])
                              );

        $content = $twig->render($this->getLangTemplatePath('email-commission-payment.html',true,$rsLang[0]['code']), $arrTwigVar);
        $this->sendMail(array(), $this->lang['commissionPayment'] . ' - ' . DOMAIN_NAME,$content,array('name' => $rsCust[0]['name'], 'email'=>$rsCust[0]['email'])); 
        
		// kirim WA
		// content WA harus bisa disetting per user
		if(!empty($this->loadSetting('WAGatewayAPIKey'))){ 
			$content = $twig->render($this->getLangTemplatePath('wa-commission-payment.html',true,$rsLang[0]['code']), $arrTwigVar);
			$content = html_entity_decode(strip_tags($content));
			
            if(!empty($rsCust[0]['mobilecode'])) $rsCust[0]['mobile'] = $rsCust[0]['mobilecode'] . $rsCust[0]['mobile'];
			$this->sendWA($rsCust[0]['mobile'],$content,true);
		}
		 
	}
    
    function updateAPPrepaid($rsHeader,$rsDetail){
        // nanti baru ditambahkan
    }
    
    
    function validateCancel($rsHeader, $autoChangeStatus = false){ 
         
        $id = $rsHeader[0]['pkey'];
        
        //$ap = $this->getAPObj();
        //$apPayableTax = $this->getPayableTaxObj();
   
        //cek ad Prepaid yg ad bukti potongnya blm 
        //$rsAPKey = $ap->getTableKeyAndObj($this->tableName);                  
		/*$rsAP = $apPayableTax->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and reftabletype = '.$rsAPKey['key'].' and ('.$apPayableTax->tableName.'.statuskey in (2,3) )');
     
		if(!empty($rsAP)) {
            $arrAP = array_column($rsAP,'code');
			$this->addErrorLog( false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' Bukti bayar sudah diinput.<br>' . implode(', ', $arrAP ).'.');
        }*/
         
		    
	 } 
	 
 
	function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey']; 
        //$this->deleteAPPrepaidTax($id); 
		
		if ($copy)
			$this->copyDataOnCancel($id);	  
		  
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	}
	
	 
	function updateGL($rs){
        if (!USE_GL) return;
        
//		$warehouse = new Warehouse();
//        $coaLink = new COALink();
//        $generalJournal = new GeneralJournal();
//        $customer = new Customer();
//        $cost = new Service(TRUCKING_SERVICE,1);
//		
//        $warehousekey = $rs[0]['warehousekey'];
//        
//        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
//		$arr = array();
//		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
//		$arr['code'] = 'xxxxx';
//		$arr['refkey'] = $rs[0]['pkey'];
//		$arr['refTableType'] = $rsKey['key'];
//		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
//		$arr['createdBy'] = 0;  
//        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
//		
//		$temp = -1;
//        $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
//        for($i=0;$i<count($rsPayment); $i++){ 
//             $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
//             $temp++;
//             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//             $arr['debit'][$temp] = 0; 
//             $arr['credit'][$temp] = $rsPayment[$i]['amount'];  
//        }
//         
//        $rsCOAOperationalCost = $coaLink->getCOALink ('operationalcost', $warehouse->tableName, $warehousekey); 
//		$rsCost = $this->getCostDetail($rs[0]['pkey']);  
//		
//		for($i=0;$i<count($rsCost); $i++){   
//             //$rsItem = $cost->getDataRowById($rsCost[$i]['costkey']);  
//             $coakey = $rsCost[$i]['coakey'] ; //(!empty($rsCost[0]['costcoakey'])) ? $rsItem[0]['costcoakey'] : $rsCOAOperationalCost[0]['coakey']; 
// 
//             $temp++;
//             $arr['hidCOAKey'][$temp] = $coakey ;
//             $arr['debit'][$temp] = $rsCost[$i]['amount']; 
//             $arr['credit'][$temp] = 0;  
//        }
//        
//         $rsCOA = $coaLink->getCOALink ('payabletax23', $warehouse->tableName,$warehousekey,0); 
//         $temp++;
//         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//         $arr['debit'][$temp] = 0; 
//         $arr['credit'][$temp] = $rs[0]['payabletax23'];  
// 
//		
//        $temp++; 
//        $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
//        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//        $arr['debit'][$temp] = 0; 
//        $arr['credit'][$temp] = $rs[0]['totaldiscount'];  
//		 
//        //selisih pembayaran   
//        $temp++; 
//        if ($rs[0]['balance'] < 0){ 
//            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
//            $arr['debit'][$temp] = 0; 
//            $arr['credit'][$temp] = abs($rs[0]['balance']); 
//        }else{ 
//            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
//            $arr['debit'][$temp] = abs($rs[0]['balance']); 
//            $arr['credit'][$temp] = 0; 
//        }
//
//        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
//        
//        
//        $temp++; 
//		$arr['hidCOAKey'][$temp] =  $customer->getAPCommissionCOAKey($rs[0]['customerkey'],$warehousekey);
//		$arr['debit'][$temp] = $rs[0]['totalpaid']; 
//		$arr['credit'][$temp] = 0;
//  
//        
//		$arrayToJs = $generalJournal->addData($arr);
//        
//		if (!$arrayToJs[0]['valid'])
//                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);
	}
	 
	
	
	function getDetailPaymentByAPKey($apkey){
		$sql = 'select 
					'. $this->tableNameDetail.'.* 
				from 
					'. $this->tableNameDetail.','. $this->tableName.'  
				where 
					'. $this->tableNameDetail.'.refkey = '. $this->tableName.'  .pkey and
					'. $this->tableNameDetail.'.apkey = ' .$this->oDbCon->paramString($apkey).' and
				    ('. $this->tableName.'.statuskey = 2 or '. $this->tableName.'.statuskey = 3)
				order by  pkey asc'; 
					  
		return $this->oDbCon->doQuery($sql);
	} 
    
    function deleteAPPrepaidTax($id){ 
          
//        $apPayableTax23 = $this->getPayableTaxObj(); 
//        $rsAP = $apPayableTax23->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and '.$apPayableTax23->tableName.'.statuskey = 1');
//        //and reftabletype = '.$this->oDbCon->paramString($rsAR[0]['reftabletype']).' 
//          
//        for($i=0;$i<count($rsAP);$i++) { 
//            $arrayToJs = $apPayableTax23->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
//            if (!$arrayToJs[0]['valid'])
//                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
//        }  
          
      }
    
    function getAPObj(){
        return new APCustomerCommission();
    }
    
    function getBussinessPartnerObj(){
        return new Customer();    
    }
    
    function getPayableTaxObj(){
        return new APPayableTax23();
    }

    function normalizeParameter($arrParam, $trim = false){
        
        $arrParam = parent::normalizeParameter($arrParam);  
        $arrParam['pph23'] = (!empty($arrParam['pph23'])) ? $arrParam['pph23'] : 0;
        $arrParam['balance'] = (!empty($arrParam['balance'])) ? $arrParam['balance'] : 0;
        $arrParam['totalPayment'] = (!empty($arrParam['totalPayment'])) ? $arrParam['totalPayment'] : 0;
        $arrParam['totalDiscount'] = (!empty($arrParam['totalDiscount'])) ? $arrParam['totalDiscount'] : 0;
        $arrParam['selPaymentMethod'] = (!empty($arrParam['selPaymentMethod'])) ? $arrParam['selPaymentMethod'] : 0;
        $arrParam['paymentMethodValue'] = (!empty($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();        
        $arrParam['hidRefKey'] = (isset($arrParam['hidRefKey'])) ? $arrParam['hidRefKey'] : 0; 
        $arrParam['downpaymentAmount'] = (!empty($arrParam['downpaymentAmount'])) ? $arrParam['downpaymentAmount'] : array();
        $arrParam['costAmount'] = (!empty($arrParam['costAmount'])) ? $arrParam['costAmount'] : array();
         
        for($i=0;$i<count($arrParam['hidDetailKey']);$i++){ 
            $arrParam['discount'][$i] = (!empty($arrParam['discount'][$i])) ? $arrParam['discount'][$i] : 0;
            $arrParam['taxPPH'][$i] = (isset($arrParam['taxPPH'][$i])) ? $arrParam['taxPPH'][$i] : 0;   
        }
           
        foreach($arrParam['paymentMethodValue'] as $key=>$row){ 
            if ($this->unFormatNumber($row) == 0){ 
                unset($arrParam['selPaymentMethod'][$key]);
                unset($arrParam['paymentMethodValue'][$key]); 
                unset($arrParam['hidDetailPaymentKey'][$key]); 
            }
        }

        $arrParam['selPaymentMethod'] = array_values($arrParam['selPaymentMethod']);
        $arrParam['paymentMethodValue'] = array_values($arrParam['paymentMethodValue']); 
        $arrParam['hidDetailPaymentKey'] = array_values($arrParam['hidDetailPaymentKey']); 

        // remove uncheck 
        $this->removeUnCheckRows($arrParam,$this->arrDataDetail);
         
        $reCountResult = $this->reCountGrandtotal($arrParam);

        $arrParam['totalPaid'] = $reCountResult['totalPaid'];
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['totalDownpayment'] = $reCountResult['totalDownpayment'];
        $arrParam['totalDiscount'] = $reCountResult['totalDiscount']; 
        $arrParam['totalCost'] = $reCountResult['totalCost'];
        $arrParam['balance'] = $reCountResult['balance']; 
        $arrParam['pph23'] = $reCountResult['pph23']; 
       
        
        return $arrParam;
    }
	
	function getDetailWithRelatedInformation($pkey,$criteria=''){
            $apObj = $this->getAPObj();
        
            $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$apObj->tableName.'.code as apcode,
                '.$apObj->tableName.'.refcode,
                '.$apObj->tableName.'.refcode2,
                '.$apObj->tableName.'.refdate, 
                '.$apObj->tableName.'.trdesc as apdesc,
                '.$this->tableAPType.'.pkey as aptypekey ,
                '.$this->tableAPType.'.name as aptypename  
			  from
			  	'.$this->tableNameDetail .', 
			  	'.$apObj->tableName .',
                '.$this->tableAPType.' 
			  where 
			  	'.$this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').') and
			  	'.$this->tableNameDetail .'.apkey = '.$apObj->tableName.'.pkey and
			  	'.$apObj->tableName.'.aptype = '.$this->tableAPType.'.pkey';
          
            $sql .= $criteria;
        
            $sql .= ' order by aptypekey asc, apcode asc ';
         
		return $this->oDbCon->doQuery($sql);
    }
	
 	function getCostDetail($pkey){
		$sql = 'select 
					'. $this->tableCost.'.* ,
					'. $this->tableItem.'.name as costname,
					'. $this->tableItem.'.coakey
				from 
					'. $this->tableCost.',
                    '.$this->tableItem.', 
                    '. $this->tableName.'  
				where 
					'. $this->tableCost.'.refkey = '. $this->tableName.'  .pkey and
                     '.$this->tableCost.'.costkey = '.$this->tableItem.'.pkey  and
					'. $this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey).'
				order by  pkey asc'; 
        
		return $this->oDbCon->doQuery($sql);
	}    
     
}

?>