<?php

class ARPayment extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ar_payment_header';
		$this->tableNameDetail = 'ar_payment_detail';
		$this->tableCustomer = 'customer';
		$this->tableEmployee = 'employee';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tablePayment= 'ar_payment';
        $this->tableDownpaymentDetail = 'ar_downpayment';
        $this->tableCost = 'ar_cost';
        $this->tableItem = 'cost_cash_out';
        $this->tableDownpayment = 'customer_downpayment';
		$this->tableAR = 'ar';
        $this->tableCurrency = 'currency';
        $this->tablePaymentMethod = 'payment_method';
        $this->tableCashBank = 'cash_bank';
		$this->tableCreditNoteHeader = 'credit_note_header';
        $this->tableTax = 'tax';

        $this->tableFile = 'ar_payment_file';
		$this->uploadFileFolder = 'ar-payment/';
        $this->isTransaction = true; 
    
        $this->useStorage = $this->useStorage('S3');		  
       
	   
	    $this->importUrl = 'import/arPayment';
	   
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tablePayment, $this->tableDownpaymentDetail,$this->tableCost);
		 
		$this->securityObject = 'ARPayment';
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['arkey'] = array('hidARKey');
        $this->arrDataDetail['outstanding'] = array('outstanding','number');
        $this->arrDataDetail['amount'] = array('amount', array('datatype' => 'number','mandatory'=>true));
        $this->arrDataDetail['discount'] = array('discount','number');
        $this->arrDataDetail['taxamount'] = array('taxPPH','number');
        $this->arrDataDetail['pphtype'] = array('selPPhType');
       
        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
        $this->arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');  // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
       
        $arrDownpaymentDetail = array(); 
        $arrDownpaymentDetail['pkey'] = array('hidDetailDownpaymentKey');
        $arrDownpaymentDetail['refkey'] = array('pkey', 'ref');
        $arrDownpaymentDetail['amount'] = array('downpaymentAmount',array('datatype' => 'number','mandatory'=>true));
        $arrDownpaymentDetail['downpaymentkey'] = array('hidDownpaymentKey',array('mandatory'=>true)); 
       
       
		$this->arrCostDetail = array(); 
		$this->arrCostDetail['pkey'] = array('hidDetailCostKey');
		$this->arrCostDetail['refkey'] = array('pkey', 'ref');
		$this->arrCostDetail['amount'] = array('costAmount',array('datatype' => 'number','mandatory'=>true));
		$this->arrCostDetail['costkey'] = array('hidCostKey',array('mandatory'=>true)); 
        
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
        array_push($arrDetails, array('dataset' => $arrDownpaymentDetail, 'tableName' => $this->tableDownpaymentDetail));
     	array_push($arrDetails, array('dataset' =>  $this->arrCostDetail, 'tableName' => $this->tableCost));
       
         
        if($this->useStorage){ 
            
            $this->arrDataFileDetail = array();  
            $this->arrDataFileDetail['pkey'] = array('hidDetailFileKey');
            $this->arrDataFileDetail['refkey'] = array('pkey','ref');
            $this->arrDataFileDetail['file'] = array('fileDetail',array('datatype' => 'file','uploadFolder' => $this->uploadFileFolder));
            
            array_push($arrDetails, array('dataset' => $this->arrDataFileDetail, 'tableName' => $this->tableFile));
        }else{ 
            array_push($arrDetails, array('dataset' => $this->arrDataFile, 'tableName' => $this->tableFile, 
                                          'datatype' => 'file', 'uploadFolder' => $this->uploadFileFolder,
                                          'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader')); 
        }
          
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['refcode'] = array('refHeaderCode');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['nettingkey'] = array('nettingkey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trnotes'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['grandtotal'] = array('grandtotal','number');
        $this->arrData['totalpayment'] = array('totalPayment','number');
        $this->arrData['totaldownpayment'] = array('totalDownpayment','number');
        $this->arrData['totalcost'] = array('totalCost','number');
        $this->arrData['totaldiscount'] = array('totalDiscount','number');
        $this->arrData['balance'] = array('balance','number');
        $this->arrData['prepaidtax23'] = array('pph23','number'); 
        $this->arrData['totalreceived'] = array('totalReceived','number');
        $this->arrData['donumbercache'] = array('doNumber');
        $this->arrData['usedateperiod'] = array('chkDatePeriod');
        $this->arrData['startdateperiod'] = array('trStartDate','date');
        $this->arrData['enddateperiod'] = array('trEndDate','date');
        $this->arrData['rate'] = array('currencyRate','number');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['ntpn'] = array('ntpn'); 
        $this->arrData['taxobjectcode'] = array('taxObjectCode'); 
        $this->arrData['taxperiod'] = array('taxPeriod','date'); 
        $this->arrData['salesordercodecache'] = array('salesordercodecache');  
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname', 'default'=>true, 'width' => 60,  'align' =>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'payingOffAmount','title' => 'payingOffAmount','dbfield' => 'totalreceived', 'default'=>true, 'width' => 120,  'align' =>'right',  'format' => 'number' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'tax23','title' => 'tax23','dbfield' => 'prepaidtax23', 'default'=>true, 'width' => 100, 'align' =>'right',  'format' => 'number' ));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'grandtotal', 'default'=>true, 'width' => 100, 'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'refCode','dbfield' => 'refcode', 'width' => 100));    
 
       
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/arPayment'));
          
        $this->includeClassDependencies(array(
                  'AR.class.php', 
                  'ARPrepaidTax23.class.php', 
                  'ARPrepaidTax23Payment.class.php',
                  'CashBank.class.php', 
                  'ChartOfAccount.class.php', 
                  'COALink.class.php', 
                  'Currency.class.php', 
                  'Customer.class.php',  
                  'Downpayment.class.php',
                  'CustomerDownpayment.class.php',  
                  'EMKLOrderInvoice.class.php', 
                  'GeneralJournal.class.php', 
                  'CostCashOut.class.php', 
                  'SalesOrder.class.php', 
                  'Service.class.php', 
                  'TruckingServiceOrderInvoice.class.php', 
                  'Warehouse.class.php',
                  'ARDiscountApproval.class.php',
                  'SalesOrderProperty.class.php',
                  'Tax.class.php'
            ));  
       
        $this->overwriteConfig();
	}
	
	function getQuery(){
		
		$sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableCustomer.'.name as customername,
			   '.$this->tableCustomer.'.taxid as customertaxid,
			   '.$this->tableCustomer.'.address as customeraddress,
			   '.$this->tableCustomer.'.taxregistrationname as customertaxregistrationname,  
			   '.$this->tableCustomer.'.taxregistrationaddress as customertaxregistrationaddress,  
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
               '.$this->tableCurrency.'.name as currencyname 
			FROM '.$this->tableStatus.',
                 '.$this->tableCustomer.', 
                  '.$this->tableName.', 
                  '.$this->tableWarehouse.',
                  '.$this->tableCurrency.'
			WHERE '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
				  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey   and
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
		' .$this->criteria ;
        
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCustomerCriteria() ;
        
        return $sql;
	}
    

	
	function reCountGrandtotal($arrParam){

				$grandtotal = 0;
				$amount = 0;
				$discount = 0;
				$pph = 0;
				
				$arrARkey = $arrParam['hidARKey'];
				$arrAmount = $arrParam['amount'];
				$arrDiscount = $arrParam['discount'];
				$arrPph = $arrParam['taxPPH'];
				//$arrPick = $arrParam['chkPick']; 
				
				$arrARDetail = array(); 
				
				for ($i=0;$i<count($arrARkey);$i++){
					
				    $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
					if ( empty($arrARkey[$i]) || empty($arrAmount[$i]))  //  || empty($arrPick[$i]) 
						continue; 
					
						$amount += $this->unFormatNumber($arrAmount[$i]);
                      
//				    if (!empty($arrPph[$i]) && $arrPph[$i]>0)  
                    
                    // agar support php23 negativ yg dibalikin (yg sudah terlanjur dipotong - logol)
                    if (!empty($arrPph[$i]))      
						$pph += $this->unFormatNumber($arrPph[$i]);
                    
				    if (!empty($arrDiscount[$i]) && $arrDiscount[$i]>0)  
						$discount += $this->unFormatNumber($arrDiscount[$i]);    
                     
				}  
        
        
                // total yg dilunasin
                $totalreceived = $amount + $discount;
        
				$totalDowpayment = 0; 
				$downpayment = $arrParam['downpaymentAmount'];
				$downpaymentKey = $arrParam['hidDownpaymentKey'];
				for($i=0;$i<count($downpayment);$i++){
                    if(empty($downpaymentKey[$i]))
                        continue;
					$totalDowpayment += $this->unFormatNumber($downpayment[$i]);
				} 
                 
                $costCashOut= new CostCashOut();
                $totalCost = 0; 
				$costAmount = $arrParam['costAmount'];
				$costKey = $arrParam['hidCostKey'];
				for($i=0;$i<count($costAmount);$i++){
                    $rsItem = $costCashOut->getDataRowById($costKey[$i]);
                    
                    if(empty($rsItem))
                        continue;
                    
					$totalCost += $this->unFormatNumber($costAmount[$i]);
				} 
         
				$grandtotal = $amount - $pph - $totalDowpayment - $totalCost; 
				
				$balance = 0;
				$totalPayment = 0; 
				$payment = $arrParam['paymentMethodValue'];
				for($i=0;$i<count($payment);$i++){
					$totalPayment += $this->unFormatNumber($payment[$i]);
				} 
          
				$balance = $totalPayment - $grandtotal;

				$reCountResult = array();
				$reCountResult['totalReceived'] = $totalreceived;
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
          
        $ARObj = $this->getARObj();
        $downpayment = new CustomerDownpayment();
            
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$customerkey = $arr['hidCustomerKey'];  
		$currencykey = $arr['selCurrency'];  
		$arrARkey = $arr['hidARKey']; 
		$arrAmount = $arr['amount'];
		$arrOutstanding= $arr['outstanding'];
		$arrDiscount = $arr['discount'];
		$arrDownpaymentKey = $arr['hidDownpaymentKey'];
		$arrDownpaymentAmount = $arr['downpaymentAmount'];
		$arrDownpaymentCode = $arr['downpaymentCode'];
        $trDate = $arr['trDate'];
		//$arrPick = $arr['chkPick'];  

        $arrDetailKey = array();
          
       // $this->setLog($arrARkey,true);
         
        $rsAR = (!empty($arrARkey)) ? $ARObj->searchData('','',true, ' and '.$ARObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrARkey)).') ') : array(); 
        
        $arrAR = array_column($rsAR,null, 'pkey');
        $arrARCustomer = array_column($rsAR, 'customerkey', 'pkey');
        $arrDate = array_column($rsAR, 'trdate', 'pkey');
            
         
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
			
		if(empty($customerkey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
	 
        $hasAR = false; 
        for($i=0;$i<count($arrARkey);$i++) { 
            if (!empty($arrARkey[$i]))  //  && !empty($arrPick[$i])
                $hasAR = true;  

            if (in_array($arrARkey[$i],$arrDetailKey)){   
                $this->addErrorList($arrayToJs,false, $arrAR[$arrARkey[$i]]['code'].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKey, $arrARkey[$i]); 
            }

        }

        if (!$hasAR)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ar'][1]); 	

        
		for($i=0;$i<count($arrARkey);$i++) {  
            if(!empty($arrARkey[$i])){
                
                $outstanding = $this->unFormatNumber($arrOutstanding[$i]);
                $amount = $this->unFormatNumber($arrAmount[$i]);
                $discount = $this->unFormatNumber($arrDiscount[$i]);

                if ($amount == 0 || 
                    ($outstanding > 0 && $amount < 0) || 
                    ($outstanding > 0 && ($amount+$discount) > $outstanding) || //overpay
                    ($outstanding < 0 && (($amount+$discount) < $outstanding ||  $amount > 0)) //overpay
                   ) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$arrAR[$arrARkey[$i]]['code']. '</strong>. ' . $this->errorMsg['arPayment'][2]);
               

                if ($arrARCustomer[$arrARkey[$i]] <> $customerkey)
                    $this->addErrorList($arrayToJs,false,$arrAR[$arrARkey[$i]]['code']. '. ' . $this->errorMsg['ar'][5]); 
                 
/*              // pelunasan dr customer tdk bisa dikontrol mau byr ke cabang mana
                if($arr['selWarehouseKey']<>$arrAR[$arrARkey[$i]]['warehousekey'])
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAR[$arrARkey[$i]]['code'].'</strong>. '.$this->errorMsg[905]); */

				
				if($currencykey<>$arrAR[$arrARkey[$i]]['currencykey'])
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAR[$arrARkey[$i]]['code'].'</strong>. '.$this->errorMsg['arPayment'][5]); 
  
                // sementara
				// utk domain2 yg gk perlu validasi tgl
				$arrExclude = array('st.wintera.co.id');
                if(!in_array(DOMAIN_NAME,$arrExclude)){
                    $arDate = $this->formatDBDate($arrDate[$arrARkey[$i]],'d / m / Y');
                    $dateDiff = $this->dateDiff($trDate,$arDate); 
                    if($dateDiff > 0)
                        $this->addErrorList($arrayToJs,false,'<strong>'.$arrAR[$arrARkey[$i]]['code'].'</strong>.'. $this->errorMsg['arPayment'][4]);
                }
                
                  
            }
		}
         
        // cek DP punya customer yg sama gk
        // cek amount yg diisi lebih besar gk dr outstanding
        
        $arrDownpaymentExistKey = array();
        
		for($i=0;$i<count($arrDownpaymentKey);$i++) {  
            if(empty($arrDownpaymentKey[$i]))
                continue;
            
            // validasi DP masi available gk
            $rsDP = $downpayment->searchData($downpayment->tableName.'.pkey',$arrDownpaymentKey[$i],true, ' and '.$downpayment->tableName.'.statuskey in (2) ');
            if(empty($rsDP)){ 
                $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][9]);
            }else{
                if ($customerkey <> $rsDP[0]['customerkey'])
                    $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][6]); 

		
				if ($currencykey <> $rsDP[0]['currencykey'])
                    $this->addErrorList($arrayToJs,false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][10]); 
                
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

                
        }
         
		
		return $arrayToJs;
	 }
	     
    function afterStatusChanged($rsHeader){ 
        
        $ARObj = $this->getARObj();
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsDetail); $i++){  
           $ARObj->updateAROutstanding($rsDetail[$i]['arkey']); 
        }
         
        // hanya berlaku utk transaksi, untuk karyawan harusnya gk berlaku
        $customerDownpayment = new CustomerDownpayment();
        $rsDownpayment = $this->getDownpaymentDetail($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsDownpayment); $i++){  
           $customerDownpayment->updateOutstanding($rsDownpayment[$i]['downpaymentkey']); 
        }
        
         // retrieve latest status
//        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
//        if ($rsHeader[0]['statuskey'] == 2)
//            $this->changeStatus($rsHeader[0]['pkey'],3,'',false,true); // set jd autochange, agar gk perlu validasi session utk AR Payment yg jalan dr cron 
    }
    
    function afterUpdateData($arrParam, $action){ 
		/*if(isset($arrParam['hidId']) && !empty($arrParam['hidId'])){ 
			$pkey = $arrParam['hidId'];  
			$this->updateARDiscountApproval($pkey);
		} */


//        $rsHeader = $this->getDataRowById($arrParam['pkey']);
//        if ($rsHeader[0]['statuskey'] < 3) {
//            $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);
//        }

        $pkey = $arrParam['pkey'];  
        $this->updateARDiscountApproval($pkey);
	}
    
    function afterAddDataOnCopy($pkey, $oldkey){  
        $this->updateARDiscountApproval($pkey);
    }
    

	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $customerkey =  $rsHeader[0]['customerkey'];
        $currencykey =  $rsHeader[0]['currencykey'];
        $rate = ($rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;
        
		$useARDiscountApproval = $this->loadSetting('useARDiscountApproval');
        
		$coaLink = new COALink();
        $warehouse = new Warehouse();
        $ar = $this->getARObj();
          
        $rsPayment = (ADV_FINANCE && TEST_VOUCHER) ?  $this->getPaymentVoucherDetail($id) : $this->getPaymentMethodDetail($id); 
      
        $rsDownpayment = $this->getDownpaymentDetail($id); 
        
        $isnetting = (isset($rsHeader[0]['nettingkey']) && !empty($rsHeader[0]['nettingkey'])) ? true : false;
        
        if($isnetting){
            $totalPayment = $rsHeader[0]['grandtotal'];
        }else{  
            $totalPayment = 0; 
            for($i=0;$i<count($rsPayment); $i++)
                $totalPayment += $rsPayment[$i]['amount'];
        }
        
        
        if($useARDiscountApproval){
            
            $thresholdDiscount = abs($this->loadSetting('arDiscountApprovalThreshold')); 
            
            $totalARDiscountApproval = 0;
            $rate = $rsHeader[0]['rate'];
                
            // ambil semua detail cost utk dicompare
            $rsCost = $this->getCostDetail($id);  
            for($i=0;$i<count($rsCost);$i++)
                $totalARDiscountApproval += ($rsCost[$i]['amount'] * $rate); 

            // ambil semua detail AR utk dicompare 
            $rsDetail = $this->getDetailWithRelatedInformation($id,' and discount > 0'); 
            for($i=0;$i<count($rsDetail);$i++) 
                $totalARDiscountApproval += ($rsDetail[$i]['discount'] * $rate); 

            
             if( $totalARDiscountApproval > $thresholdDiscount){ 
                // asumsi  1 AR cuma boleh punya 1 approval
                $arDiscountApproval = new ARDiscountApproval();
                $rsCreditNote = $arDiscountApproval->searchData('','',true,'  and '.$arDiscountApproval->tableName.'.refkey = '.$this->oDbCon->paramString($id).' and '.$arDiscountApproval->tableName.'.statuskey in (2,3)');
                if(empty($rsCreditNote)) 
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' .$this->errorMsg['arPayment'][6]); 
            }
            
		}
          
        $balance = $totalPayment - $rsHeader[0]['grandtotal']; 
        $balance *= $rate; 
         
        $thresholdDiscount = abs($this->loadSetting('roundedPaymentThreshold'));
         
        if($balance < ($thresholdDiscount * -1)) 
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[502]);
        else if ($balance > $thresholdDiscount)
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[509]); 

        if (USE_GL){ 

            $arrCOA = array();
            array_push($arrCOA, 'ar' , 'otherrevenue','othercost', 'prepaidtax23', 'customerdownpayment'); 
            
            // kalo ad lebih dr 1 currency
            $currency = new Currency();
            $rsCurrency = $currency->searchData($currency->tableName.'.statuskey',1);
            if (!count($rsCurrency) > 1)
                array_push($arrCOA, 'lossprofitrate');
                       
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }   
        
            if (ADV_FINANCE && TEST_VOUCHER){
                for($i=0;$i<count($rsPayment); $i++){ 
                    // cek kalo customerkey sudah beda
                    
                    if ($rsPayment[$i]['vouchercustomerkey'] <> $customerkey && $rsPayment[$i]['vouchercustomerkey'] <> 0)
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][3]); 
                    else if ($rsPayment[$i]['voucheroutstanding'] < $rsPayment[$i]['amount'])
                        // cek kalo outstanding masih cukup
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][4]); 
                    
                    else if ($rsPayment[$i]['voucherstatuskey'] <> TRANSACTION_STATUS['konfirmasi'])
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][5]); 
                 
                }  
            }else{ 
                for($i=0;$i<count($rsPayment); $i++){ 
                    if ($rsPayment[$i]['amount'] > 0 ){ 
                        $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                        if (empty($rsCOA))	
                            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]); 
                    }
                }  
            }
        }    
 
        $rsDetail = $this->getDetailById($id);
        $arrKeys = array_column($rsDetail,'arkey');  
        $rsAR = $ar->searchData('','',true,' and ' .$ar->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') ' );
      
        
        // cek status piutang sudah lunas atau blm
        if (!empty($arrKeys)){ 
            $rsARPaid = $ar->searchData('','',true,' and ' .$ar->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') and ' .$ar->tableName.'.statuskey in (3,4) ' );
            if (!empty($rsARPaid)){
                $arrAR = array_column($rsARPaid,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br><strong>'.implode(', ',$arrAR).'</strong>. '.$this->errorMsg['ar'][6]); 
            }
        }
        
        // cek DP punya customer yg sama gk
        // cek amount yg diisi lebih besar gk dr outstanding
		for($i=0;$i<count($rsDownpayment);$i++) {   
            
            // validasi DP masi available gk 
            if($rsDownpayment[$i]['downpaymentstatuskey'] <> 2){ 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][9]);
            }else{
                if ($customerkey <> $rsDownpayment[$i]['downpaymentcustomerkey'])
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][6]); 

				if ($currencykey <> $rsDownpayment[$i]['downpaymentcurrencykey'])
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][10]); 

                // validasi nilai DP masi mencukupi gk 
                if ($rsDownpayment[$i]['amount'] > $rsDownpayment[$i]['downpaymentoutstanding'] )
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDownpayment[$i]['downpaymentoutstanding']).')');  
            }
                
        }
        
        $trDate =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        for($i=0;$i<count($rsAR);$i++){
            /*
            // pelunasan dr customer tdk bisa dikontrol mau byr ke cabang mana
            if($rsHeader[0]['warehousekey']<>$rsAR[$i]['warehousekey'])
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$rsAR[$i]['code'].'</strong>. '.$this->errorMsg[905]); */
 
			
			if($currencykey<>$rsAR[$i]['currencykey'])
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$rsAR[$i]['code'].'</strong>. '.$this->errorMsg['arPayment'][5]); 

			$arrExclude = array('st.wintera.co.id');
			if(!in_array(DOMAIN_NAME,$arrExclude)){
				$arDate = $this->formatDBDate($rsAR[$i]['trdate'],'d / m / Y');
				$dateDiff = $this->dateDiff($trDate,$arDate);
				if($dateDiff > 0)
					$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['arPayment'][4]); 
			}
           
        }
        
        
        // cek setiap detail ad g overpaid gk
        // harus ambil ulang outstanding AR
        $rsAR = array_column($rsAR,null,'pkey');
        foreach($rsDetail as $detailRow){
           $arRow = $rsAR[$detailRow['arkey']];
            
           //$this->setLog($detailRow['amount'].'+'.$detailRow['discount'] .'>'. $arRow['outstanding'],true);
            
           if ( ($arRow['outstanding'] > 0 && ($detailRow['amount']+$detailRow['discount']) > ($arRow['outstanding']+1) ) || //overpay
               ($arRow['outstanding']  < 0 && (($detailRow['amount']+$detailRow['discount']) < ($arRow['outstanding']-1)  ||  $detailRow['amount'] > 0)) //overpay
              )   
            $this->addErrorLog(false,'<strong>'.$arRow['code']. '</strong>. ' . $this->errorMsg['arPayment'][2]); 
           
        }
         
        
    }
	 
	function confirmTrans($rsHeader){
		$id = $rsHeader[0]['pkey']; 
        $coaLink = new COALink();
		$warehouse = new Warehouse();   
		$customer = new Customer();
        $cashBank = new CashBank();
		//$cashMovement = new CashMovement();  
		
        $rsCustomer = $customer->getDataRowById($rsHeader[0]['customerkey']);
		$notecash = $rsHeader[0]['code'].'. Kas Masuk untuk pembayaran piutang dari '.$rsCustomer[0]['name'];
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
		
		
		$arrCashBank = array();
		
		// MENGHITUNG PAYMENT
        
        if (ADV_FINANCE && TEST_VOUCHER){ 
            $rsPayment = $this->getPaymentVoucherDetail($id);
            
            $rsARKey = $this->getTableKeyAndObj($this->tableName,array('key'));    
            
            // update outstanding voucher  
            foreach($rsPayment as $voucherlist){ 
                $cashBank->insertTransaction(
                    array('refkey' => $voucherlist['cashbankvoucherkey'],
                          'reftablekey' => $rsARKey['key'],
                          'reftranskey' => $rsHeader[0]['pkey'],
                          'refcode' => $rsHeader[0]['code'],
                          'refdate' => $rsHeader[0]['trdate'],
                          'amount' => $voucherlist['amount'],
                         )
                ); 
            }

        }else{
            $rsPayment = $this->getPaymentMethodDetail($id);    
            for($i=0;$i<count($rsPayment); $i++){  
				   if (USE_GL) {
					   $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
					   $coakey = $rsPaymentCOA[0]['coakey']; 
				   }else{
					   $coakey = $rsPayment[$i]['paymentkey'];
				   }	
				
				if( $this->isActiveModule('CashBank') ){
					$cashBank = new CashBank(); 
					 if (USE_GL) {
					   $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
					   $coakey = $rsPaymentCOA[0]['coakey']; 
				     }else{
					   $coakey = $rsPayment[$i]['paymentkey'];
				     }


					$rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array( 'customerkey' => $rsHeader[0]['customerkey'], 'coakey' => $coakey , 'amount' => $rsPayment[$i]['amount'])); 
					$rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey']; 
				}


                //$cashMovement->updateCashMovement($id, $coakey,$rsPayment[$i]['amount'],$this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
            }           
        } 
   
		// END  
		
		//update jurnal umum 
        $this->updateGL($rsHeader,$rsPayment);
        
        if ($rsHeader[0]['prepaidtax23'] != 0) 
            $this->updateAPPrepaid($rsHeader,$rsDetail); 
		
	}
    
    function updateAPPrepaid($rsHeader,$rsDetail){
        
            $arPrepaidTax23 = $this->getPrepaidTaxObj();  
            $ar = $this->getARObj();
            $rate = (isset($rsHeader[0]['rate']) && $rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;
   
            $tax = new Tax();

            $rsTax = $tax->searchDataRow(array( $tax->tableName.'.pkey', $tax->tableName. '.name', $tax->tableName. '.haswithholding' ), 
                                        ' and ' . $tax->tableName.'.typekey = '. $this->oDbCon->paramString(TAX_TYPE['PPH']) .' and '. $tax->tableName .'.statuskey = 1');
            $rsTax = $this->reindexDetailCollections($rsTax, 'pkey');
        
            for ($i=0;$i<count($rsDetail);$i++){ 
                $pphTypeKey = 0; // reset ulang
                
                // hanya jika detail pph ada isinya (backcompability)
                if(!empty($rsDetail[$i]['pphtype'])){ 
                    $pphTypeKey = $rsDetail[$i]['pphtype']; 
                    $hasWithholding = $rsTax[$pphTypeKey][0]['haswithholding']; 
                    if($hasWithholding != 1) continue;
                }
                 
                if ($rsDetail[$i]['taxamount'] == 0)  continue;
                    
                $arrParam = array();
                
                $rsAR = $ar->getDataRowById($rsDetail[$i]['arkey']); 
                 
                // kalo add manual, gk ad obj
                $rsSO = array();
                if (!empty($rsAR[0]['reftabletype'])){
                    // cek tipe AR  
//                    $type = $this->getTableNameAndObjById($rsAR[0]['reftabletype']);
//                    $salesObj = $type['obj'];  
                    
                    // ganti pake getObjMapping
                    $salesObj = $this->getObjMapping('',$rsAR[0]['reftabletype']); 
                    $rsSO = $salesObj->getDataRowById($rsAR[0]['refkey']); 
                }
                
                
                $rsARKey = $this->getTableKeyAndObj($this->tableName,array('key'));      
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidCustomerKey'] = $rsHeader[0]['customerkey']; 
                $arrParam['hidRefKey'] =  $rsDetail[$i]['pkey']; // $rsSO[0]['pkey']; kepake gk ??? harusnya ambil detailkey dr payment
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =   (!empty($rsSO)) ? $rsSO[0]['code'] : '';
                $arrParam['hidRefDate'] =   (!empty($rsSO)) ? $this->formatDBDate($rsSO[0]['trdate'],'d / m / Y') : DEFAULT_EMPTY_DATE;  
                $arrParam['hidRefTable'] = $rsARKey['key'];
                $arrParam['amount'] = $rsDetail[$i]['taxamount'] * $rate;
                $arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['dueDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['createdBy'] = 0;
                $arrParam['islinked'] = 1;
                $arrParam['selARType'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['selPPhType'] = $pphTypeKey;
                

                $returnVal = $arPrepaidTax23->addData($arrParam,false);  
 
            }  
    }

    function validateCancel($rsHeader,$autoChangeStatus = false){ 
            

            if ( !$autoChangeStatus ) {
                if(isset($rsHeader[0]['islinked']) && !empty($rsHeader[0]['islinked']))
                    $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[900],true);  
            }  

            $id = $rsHeader[0]['pkey'];
        
            if(!$this->validateAutoReverseGL($id))
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['generalJournal'][6],true);
        
            $ar = $this->getARObj();
            $arPrepaidTax = $this->getPrepaidTaxObj();

            //cek ad Prepaid yg ad bukti potongnya blm
            $rsARKey = $ar->getTableKeyAndObj($this->tableName,array('key')); 
            $rsAR = $arPrepaidTax->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and reftabletype = '.$rsARKey['key'].' and ('.$arPrepaidTax->tableName.'.statuskey in (2,3) )');
			if(!empty($rsAR)){
                $arrAR = array_column($rsAR,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><b>' . implode(', ', $arrAR ).'</b>. '.$this->errorMsg['arPrepaid23'][3]); 
            } 

    } 

    function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey'];   

        $rsARKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			if (ADV_FINANCE && TEST_VOUCHER){ 
				$cashBank->removeTransaction($id,$rsARKey['key']);
			}else{ 
				$cashBank->cancelCashBank($rsHeader,$this->tableName);
			}
		}
		
        $this->deleteAPPrepaidTax($id); 
        $this->cancelCreditNote($id); 
 
        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }


	function updateGL($rs,$rsPayment){ 
         
		
        if (!USE_GL) return;
        if ($rs[0]['overwriteGL'] == 1) return;
        
		$warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal(); 
		$customer = new Customer();
        $cashBank = new CashBank();
        $chartOfAccount = new ChartOfAccount();
        $costCashOut= new CostCashOut();
        
        $paymentCurrencyKey = $rs[0]['currencykey'];
        
        $multiCurrency = ($rs[0]['currencykey'] != CURRENCY['idr']) ? true : false; // khusus currency selain IDR
        
        $totalPayment = 0;
        
        $warehousekey = $rs[0]['warehousekey'];
        $rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;
        
        $totalDiscount = $rs[0]['totaldiscount'] * $rate;
            
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =   $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0; 
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
        // desc
        $desc = array(); 
        $rsCustomer = $customer->getDataRowById($rs[0]['customerkey']);
        array_push($desc,html_entity_decode($rsCustomer[0]['name'])); 
        if(!empty($rs[0]['trnotes'])) array_push($desc,$rs[0]['trnotes']);
		$arr['trDesc'] = implode(chr(13),$desc);
		
		$temp = -1;
		$totalPaymentAmount = 0;
		
		// khusus logol
		if(ADV_FINANCE && TEST_VOUCHER) 
			$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey']);
		
		
		for($i=0;$i<count($rsPayment); $i++){ 
			// khusus logol
			// adv_finance, payment menggunakan voucher
			
			if(ADV_FINANCE && TEST_VOUCHER){
//				$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey']); // harusnya udah gk perlu
				
				$rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
				$rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);
				$paymentcoakey = $rsCOA[0]['countercoakey'];
                $paymentRate = $rsCashBank[0]['rate'];  
                
			}else{
				$rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
				$paymentcoakey = $rsCOA[0]['coakey'];
                $paymentRate = $rs[0]['rate']; //  pake header rate, seperti diawal sebelum pake modul voucher
			}
			 
			 $temp++; 
			 $paymentAmount = $rsPayment[$i]['amount'] * $paymentRate;
			 $arr['hidCOAKey'][$temp] = $paymentcoakey;
			 $arr['debit'][$temp] = $paymentAmount; 
			 $arr['credit'][$temp] = 0; 
             $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
             $arr['debitSource'][$temp] = $rsPayment[$i]['amount']; 
             $arr['creditSource'][$temp] = 0; 
             $arr['rate'][$temp] = $paymentRate ; 
			 $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  // perlu dicek gk logol sama atau gk
			 $totalPaymentAmount += $paymentAmount;
		}
		
//		$rsPayment = (ADV_FINANCE && TEST_VOUCHER) ? $this->getPaymentVoucherDetail($rs[0]['pkey']) 
//                                                    : $this->getPaymentMethodDetail($rs[0]['pkey']);  
//             
//        $totalPaymentAmount = 0;
//        
//        // kalo pake voucher, potong counter coa
//        for($i=0;$i<count($rsPayment); $i++){ 
//             if(ADV_FINANCE && TEST_VOUCHER){
//                 $rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
//                 $rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);
//                 $paymentcoakey = $rsCOA[0]['countercoakey']; 
//             }else{ 
//                 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']);  
//                 $paymentcoakey = $rsCOA[0]['coakey'];
//             }
//            
//             $paymentAmount = $rsPayment[$i]['amount'] * $rate;
//             $temp++;
//             $arr['hidCOAKey'][$temp] = $paymentcoakey;
//             $arr['debit'][$temp] = $paymentAmount; 
//             $arr['credit'][$temp] = 0;  
//             
//             $totalPaymentAmount += $paymentAmount;
//        }
		   
		$rsDownpayment = $this->getDownpaymentDetail($rs[0]['pkey']);  
        for($i=0;$i<count($rsDownpayment); $i++){  
            
             $dwnpaymentRate = $rsDownpayment[$i]['downpaymentrate'];
            
             $downpaymentAmount = $rsDownpayment[$i]['amount'] * $dwnpaymentRate;
            
             $temp++;
             $arr['hidCOAKey'][$temp] = $customer->getDownpaymentCOAKey($rs[0]['customerkey'],$warehousekey);   
             // tetep ambil dr rate ketika terjadi DP, karena kalo ad multi rate di AR, kita gk bisa hitung
             $arr['debit'][$temp] = $downpaymentAmount;
             $arr['credit'][$temp] = 0;  
             $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
             $arr['debitSource'][$temp] = $rsDownpayment[$i]['amount']; 
             $arr['creditSource'][$temp] = 0 ; 
             $arr['rate'][$temp] = $dwnpaymentRate ;  
             $arr['refCashBankKey'][$temp] = '';  
            
            
             $totalPaymentAmount += $downpaymentAmount;
        }

        $rsCOAOperationalCost = $coaLink->getCOALink ('operationalcost', $warehouse->tableName, $warehousekey); 
		$rsCost = $this->getCostDetail($rs[0]['pkey']);  
        for($i=0;$i<count($rsCost); $i++){   
             $rsItem = $costCashOut->getDataRowById($rsCost[$i]['costkey']);  
             $coakey = (!empty($rsItem[0]['coakey'])) ? $rsItem[0]['coakey'] : $rsCOAOperationalCost[0]['coakey']; 
 
             $costAmount = $rsCost[$i]['amount'] * $rate;
            
             $temp++;
             $arr['hidCOAKey'][$temp] = $coakey ;
             $arr['debit'][$temp] = $costAmount; 
             $arr['credit'][$temp] = 0;  
             $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
             $arr['debitSource'][$temp] = $rsCost[$i]['amount']; 
             $arr['creditSource'][$temp] = 0; 
             $arr['rate'][$temp] = $rate ; 
             $arr['refCashBankKey'][$temp] = '';   
            
             $totalPaymentAmount += $costAmount;
        }

         $tax23Amount = $rs[0]['prepaidtax23'] * $rate; 
         $totalPaymentAmount += $tax23Amount;
        
         //$tax23COA = ($this->loadSetting('tax23GLInInvoice') == 1 ) ? 'prepaidtax23Counter': 'prepaidtax23';
         //$rsCOA = $coaLink->getCOALink ($tax23COA, $warehouse->tableName,$warehousekey,0); 
         //$temp++;
         //$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
         //$arr['debit'][$temp] = $tax23Amount;  
         //$arr['credit'][$temp] = 0;  
         //$arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
         //$arr['debitSource'][$temp] =  $rs[0]['prepaidtax23']; 
         //$arr['creditSource'][$temp] = 0; 
         //$arr['rate'][$temp] = $rate ; 
         //$arr['refCashBankKey'][$temp] = '';  

        // model default di warehouse
        //$tax23COA = ($this->loadSetting('tax23GLInInvoice') == 1 ) ? 'prepaidtax23Counter': 'prepaidtax23';
        //$warehouseTaxCOAKey = $coaLink->getCOALink ($tax23COA, $warehouse->tableName,$warehousekey,0)[0]['coakey'];  
                        
                        
        $arrPPHTypeKey = array();
        $rsDetail = $this->getDetailById($rs[0]['pkey']);
        $arrPPHTypeKey = array_column($rsDetail, 'pphtype' );
        
        
        //PPH
        if($rs[0]['prepaidtax23'] != 0){
            $tax = new Tax();
            $rsCOA = $tax->getPPhCOA($arrPPHTypeKey, $rs[0]['warehousekey']);
            $rsCOACols = array_column($rsCOA,null,'pkey');
           
            $arrTaxAmount = array();
            for($i=0;$i<count($rsDetail);$i++){ 
                $pphTypeKey = (isset($rsDetail[$i]['pphtype'])) ? $rsDetail[$i]['pphtype'] : 0 ;  // defaultnya 0 kalo gk ad jenis tax
                $taxAmount = $rsDetail[$i]['taxamount']; 
                $taxCOAKey = $rsCOACols[$pphTypeKey]['coakey'];    
                
                //// kalo kosong tembak pph23 default warehouse
                //if(!empty($rsCOACols[$pphTypeKey])){
                //    //$this->setLog('model baru',true);    
                //    $taxCOAKey = $rsCOACols[$pphTypeKey]['coakey'];    
                //}else{
                //    // model lama
                //   //$this->setLog('model lama',true);    
                //    $taxCOAKey = $warehouseTaxCOAKey;
                //}
                 
                if(!isset($arrTaxAmount)) $arrTaxAmount[$taxCOAKey] = 0;
                
                $arrTaxAmount[$taxCOAKey] += $taxAmount;
            }
            
            foreach($arrTaxAmount as $taxCOAKey=>$taxAmount){
                $temp++;
                $arr['hidCOAKey'][$temp] = $taxCOAKey; 
                $arr['debit'][$temp] = $taxAmount * $rate; 
                $arr['credit'][$temp] = 0;  
                $arr['debitSource'][$temp] = $taxAmount; 
                $arr['creditSource'][$temp] =  0; 
                $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey; 
                $arr['rate'][$temp] = $rate ; 
                $arr['refCashBankKey'][$temp] = '';
            }
             
               
        }

        
        $temp++; 
        $rsCOA = $coaLink->getCOALink ('salesretaildiscount', $warehouse->tableName,$warehousekey, 0); // ini harus dipisah antara jasa / retail sepertinya
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $totalDiscount; 
        $arr['credit'][$temp] = 0;  
        $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
        $arr['debitSource'][$temp] = $rs[0]['totaldiscount']; 
        $arr['creditSource'][$temp] = 0; 
        $arr['rate'][$temp] = $rate ; 
        $arr['refCashBankKey'][$temp] = '';  

        //selisih pembayaran    
        $balance = abs($rs[0]['balance']) * $rate; 
        $temp++; 
        if ($rs[0]['balance'] < 0){  
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = $balance; 
            $arr['credit'][$temp] = 0;  
            $arr['debitSource'][$temp] = abs($rs[0]['balance']) ; 
            $arr['creditSource'][$temp] = 0; 
            $arr['refCashBankKey'][$temp] = '';  
            
            // akan aneh kalo ad selisih pembayran, harus disesuaikan dengan selisih pembayaran
            $totalPaymentAmount += $balance;
        }else { 
            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $balance;
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] =   abs($rs[0]['balance']) ; 
            $arr['refCashBankKey'][$temp] = '';  
            
            // akan aneh kalo ad selisih pembayran, harus disesuaikan dengan selisih pembayaran
            $totalPaymentAmount -= $balance; 
        }

        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
        $arr['rate'][$temp] = $rate ; 
        
        $ar = $this->getARObj();
        $rsDetail = $this->getDetailById($rs[0]['pkey']);
        
        $totalAr = 0;
        $totalArSource = 0;
        
        $totalArReimburse = 0;
        $totalArSourceReimburse = 0;
        $totalDiscountReimburse = 0;
        
        $rsARCol = $ar->searchDataRow(array($ar->tableName.'.pkey', $ar->tableName.'.amountidr', $ar->tableName.'.amount', $ar->tableName.'.rate', $ar->tableName.'.reftabletype', $ar->tableName.'.artype'),
                                      ' and '.$ar->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'arkey'),',').')'
                                     );
        $rsARCol = array_column($rsARCol,null,'pkey');
        
            
        // kalo ad laba rugi langsung dihitung saja selisihnya,
        //gk bisa dihitung per AR< akan ad masalah kalo ad DP USD
         
         
		//$arrARGroupByType = array();
        
		
        // split, AR Reimburse di split atau gk 
        $splitARReimburse = $this->loadSetting('splitARReimbursement');
		$splitARReimburse = ($splitARReimburse == 1) ? true : false ;
        
        
        foreach($rsDetail as $key=>$rowDetail){ 
             
            $rsAr = $rsARCol[$rowDetail['arkey']];
			$subtotalAr = ($rowDetail['amount'] * $rsAr['rate']);
            $subtotalArSource = $rowDetail['amount'];
            
            
			//$subtotalDiscount = $rowDetail['discount'] * $rate; // discount pake rate payment
				
            if($splitARReimburse && $rsAr['artype'] == AR_TYPE['reimburse']){ 
                $totalArReimburse += $subtotalAr;
                $totalArSourceReimburse += $subtotalArSource; 
                $totalDiscountReimburse += $rowDetail['discount'];
            }else{
                $totalAr += $subtotalAr;
                $totalArSource += $subtotalArSource; 
            }
            
			// group berdasarkan jenisnya, agar counter jurnalnya ketauan
//            $arTypeKey = $rsAr['artype'];
//			if((!isset($arrARGroupByType[$arTypeKey]))){
//				  
//				$coakey = $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey,$artypekey);
//				
//				$arrARGroupByType[$arTypeKey] = array();
//				$arrARGroupByType[$arTypeKey]['coakey'] = $coakey;
//				$arrARGroupByType[$arTypeKey]['amount'] = 0;
//			}	
//			
//			$arrARGroupByType[$arTypeKey]['amount'] += ($subtotalAr + $subtotalDiscount);
			
        }
         
 
        // akan aneh kalo ad selisih pembayran, harus disesuaikan dengan selisih pembayaran
        $totalPaymentAmount -= $balance; 
          
        // jika $totalDifference > 0 berarti laba kurs 
        $totalDifference = $totalPaymentAmount - $totalAr;
 
        // kalo reimbruse ada diskon, dikeluarin diskonnya agar gk kecatat 2x
        $totalDiscount -= $totalDiscountReimburse;
            
        if ($totalArReimburse > 0){
            $temp++; 
            $arr['hidCOAKey'][$temp] =  $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey,true);
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $totalArReimburse - $totalDiscountReimburse; // dulu ini totalreceived  
            $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] = $totalArSourceReimburse; 
            $arr['rate'][$temp] =  $totalArReimburse / $totalArSourceReimburse ; // rate rata2
            $arr['refCashBankKey'][$temp] = ''; 
        }
         
        $temp++; 
        $arr['hidCOAKey'][$temp] =  $customer->getARCOAKey($rs[0]['customerkey'],$warehousekey);
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $totalAr + $totalDiscount; // dulu ini totalreceived 
        $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
        $arr['debitSource'][$temp] = 0; 
        $arr['creditSource'][$temp] = $totalArSource; 
        $arr['rate'][$temp] =  $totalAr / $totalArSource ; // rate rata2
        $arr['refCashBankKey'][$temp] = '';     


		// beda jenis AR, beda coa pelunasan, skrg baru ad 1 jenis AR jd gk perlu
//		foreach($arrARGroupByType as $key=>$arTypeRow){
//			$temp++; 
//			$arr['hidCOAKey'][$temp] = $arTypeRow['coakey'];
//			$arr['debit'][$temp] = 0; 
//			$arr['credit'][$temp] = $arTypeRow['amount'];// dulu ini totalreceived
//		}

 
        if($multiCurrency){
			 
			// $totalDifference akan terhitugn dua kalo dengan kelebihan bayar
			// meskipun hailnya sama, tp ad perulangan coa
			// jadi potong dulu dengan selisih pembayaran agar gk double
			$balance = $rs[0]['balance'] * $rate;
			$totalDifference += $balance;  
			
			if($totalDifference <> 0){ 
             $rsCOA = $coaLink->getCOALink ('lossprofitrate', $warehouse->tableName,$warehousekey, 0); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];     
             $arr['debit'][$temp] = 0; 
             $arr['credit'][$temp] = $totalDifference; // plus minus nanti otomatis dibalik, jgn di if else, malah jd error
                
             $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
             $arr['debitSource'][$temp] = 0; 
             $arr['creditSource'][$temp] = $totalDifference; 
             $arr['rate'][$temp] = 1 ;
             $arr['refCashBankKey'][$temp] = '';  
			}
        }
        
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 

    }
      
	function getDetailPaymentByARKey($arkey,$criteria = ''){
		$sql = 'select 
                    '. $this->tableName.'.code,  
                    '. $this->tableName.'.refcode,   
                    '. $this->tableName.'.ntpn,  
                    '. $this->tableName.'.trdate,   
					'. $this->tableNameDetail.'.*,
					'. $this->tableCurrency.'.name as currencyname
				from 
					'. $this->tableNameDetail.',
					'. $this->tableCurrency.',
                    '. $this->tableName.'  
				where 
					'. $this->tableNameDetail.'.refkey = '. $this->tableName.'  .pkey and
					'. $this->tableCurrency.'.pkey = '. $this->tableName.'  .currencykey and
					'. $this->tableNameDetail.'.arkey in (' .$this->oDbCon->paramString($arkey,',').') and
				    ('. $this->tableName.'.statuskey = 2 or '. $this->tableName.'.statuskey = 3) ';
        
        if(!empty($criteria))
            $sql .= $criteria;   
        
        $sql .= ' order by  pkey asc'; 
					  
		return $this->oDbCon->doQuery($sql);
	}  
	
    function deleteAPPrepaidTax($id){ 
          
		
        $rsARKey = $this->getTableKeyAndObj($this->tableName,array('key'))['key']; 
		
        $arPrepaidTax23 = $this->getPrepaidTaxObj(); 
        $rsAR = $arPrepaidTax23->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and  reftabletype = '.$this->oDbCon->paramString($rsARKey).' and '.$arPrepaidTax23->tableName.'.statuskey = 1');
       
        for($i=0;$i<count($rsAR);$i++) { 
            $arrayToJs = $arPrepaidTax23->changeStatus($rsAR[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }  
          
      }


    function getARObj(){
        return new AR();
    }
    
    function getPrepaidTaxObj(){
        return new ARPrepaidTax23();
    }
	
    
    function normalizeParameter($arrParam, $trim = false){
		
		$arrParam = parent::normalizeParameter($arrParam);
        
        $arrParam['pph23'] = (!empty($arrParam['pph23'])) ? $arrParam['pph23'] : 0;
        $arrParam['balance'] = (!empty($arrParam['balance'])) ? $arrParam['balance'] : 0;
        $arrParam['totalPayment'] = (!empty($arrParam['totalPayment'])) ? $arrParam['totalPayment'] : 0;
        $arrParam['totalDiscount'] = (!empty($arrParam['totalDiscount'])) ? $arrParam['totalDiscount'] : 0;
        $arrParam['selPaymentMethod'] = (!empty($arrParam['selPaymentMethod'])) ? $arrParam['selPaymentMethod'] : array();
        $arrParam['paymentMethodValue'] = (!empty($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();
        $arrParam['downpaymentAmount'] = (!empty($arrParam['downpaymentAmount'])) ? $arrParam['downpaymentAmount'] : array();
  	    $arrParam['costAmount'] = (!empty($arrParam['costAmount'])) ? $arrParam['costAmount'] : array();
  	    $arrParam['hidDownpaymentKey'] = (!empty($arrParam['hidDownpaymentKey'])) ? $arrParam['hidDownpaymentKey'] : array();
  	    $arrParam['hidCostKey'] = (!empty($arrParam['hidCostKey'])) ? $arrParam['hidCostKey'] : array();
        $arrParam['trStartDate'] = (!empty($arrParam['trStartDate'])) ? $arrParam['trStartDate'] : DEFAULT_EMPTY_DATE;  
        $arrParam['trEndDate'] = (!empty($arrParam['trEndDate'])) ? $arrParam['trEndDate'] : DEFAULT_EMPTY_DATE;
        $arrParam['selCurrency'] = (!empty($arrParam['selCurrency'])) ? $arrParam['selCurrency'] : CURRENCY['idr'];
        $arrParam['islinked'] = (!empty($arrParam['islinked'])) ? $arrParam['islinked'] : 0;
        $arrParam['taxPeriod'] = (!empty($arrParam['taxPeriod'])) ? date('01 / m / Y',strtotime($_POST['taxPeriod'])) : DEFAULT_EMPTY_DATE;  
        
        
        $arrDONumber = array();
        $arrJOCodeCache = array();
        $ar = $this->getARObj();
        for($i=0;$i<count($arrParam['hidDetailKey']);$i++){ 
            $arrParam['discount'][$i] = (!empty($arrParam['discount'][$i])) ? $arrParam['discount'][$i] : 0;
            $arrParam['taxPPH'][$i] = (isset($arrParam['taxPPH'][$i])) ? $arrParam['taxPPH'][$i] : 0;  
            
            $rsAr = $ar->getDataRowById($arrParam['hidARKey'][$i]); 
            $arrJOCode = explode(',',$rsAr[0]['salesordercodecache']);
            $rsAr = explode(',',$rsAr[0]['refcode2']);
            
            foreach($rsAr as $arRow)
                if(!empty($arRow))  array_push($arrDONumber, $arRow);  

            foreach($arrJOCode as $JOCodeRow)
                if(!empty($JOCodeRow))  array_push($arrJOCodeCache, $JOCodeRow);  
        } 
        
        
        
        // kalo netting 
        $isnetting = (isset($arrParam['nettingkey']) && !empty($arrParam['nettingkey'])) ? true : false;
        
        
        if( !$isnetting ){  
            foreach($arrParam['paymentMethodValue'] as $key=>$row){ 
                if ($this->unFormatNumber($row) == 0){ 
                    unset($arrParam['selPaymentMethod'][$key]);
                    unset($arrParam['selVoucher'][$key]);
                    unset($arrParam['paymentMethodValue'][$key]); 
                    unset($arrParam['hidDetailPaymentKey'][$key]); 
                }
            }
            
            $arrParam['selPaymentMethod'] = (isset($arrParam['selPaymentMethod']))  ? array_values($arrParam['selPaymentMethod']) : array();  
            $arrParam['selVoucher'] = (isset($arrParam['selVoucher']))  ? array_values($arrParam['selVoucher']) : array();
            $arrParam['paymentMethodValue'] = array_values($arrParam['paymentMethodValue']); 
            $arrParam['hidDetailPaymentKey'] = array_values($arrParam['hidDetailPaymentKey']); 
        }else{
            $arrParam['selPaymentMethod'] = array();
            $arrParam['selVoucher'] = array();
            $arrParam['paymentMethodValue'] = array();
            $arrParam['hidDetailPaymentKey'] = array(); 
        }
        
 
        $arrParam['doNumber'] = implode(', ' ,$arrDONumber);
        $arrJOCodeCache = array_unique($arrJOCodeCache); 
        $arrParam['salesordercodecache'] = implode(', ' ,$arrJOCodeCache);
        
        // remove uncheck 
		
		// hanya jika bkn dr API
		if (isset($arrParam['_mnv-api']) && !empty($arrParam['_mnv-api'])){ 
			
		}else{
			$this->removeUnCheckRows($arrParam,$this->arrDataDetail);
		}	
        	
        
        $reCountResult = $this->reCountGrandtotal($arrParam);
         
        $arrParam['totalReceived'] = $reCountResult['totalReceived'];
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['totalDownpayment'] = $reCountResult['totalDownpayment'];
        $arrParam['totalDiscount'] = $reCountResult['totalDiscount'];
        $arrParam['totalCost'] = $reCountResult['totalCost'];
        $arrParam['balance'] = $reCountResult['balance']; 
        $arrParam['pph23'] = $reCountResult['pph23']; 
        
        if( $isnetting ){  
            $arrParam['selPaymentMethod'] = array('0' => -1);
            $arrParam['paymentMethodValue'] = array('0' => $arrParam['grandtotal']);
            $arrParam['totalPayment'] = $arrParam['grandtotal'];
            $arrParam['hidDetailPaymentKey'] = array('0' => 0);
            $arrParam['balance'] = 0; 
        }
		
        return $arrParam;
    }
	
    function getCostDetail($pkey){
		$sql = 'select 
					'. $this->tableCost.'.* ,
					'. $this->tableItem.'.name as costname
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
    
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
      $arObj = $this->getARObj();
        
      $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$arObj->tableName.'.code as arcode ,
                '.$arObj->tableName.'.refcode  ,
                '.$arObj->tableName.'.refdate
			  from
			  	'. $this->tableNameDetail .',
                '.$arObj->tableName.' 
			  where
			  	'. $this->tableNameDetail .'.arkey = '.$arObj->tableName.'.pkey and
			  	'. $this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').') ';
         
       
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   } 
     
    function getDetailPaymentCollections($rs,$indexField,$criteria=''){ 
        $rsAllDetail = $this->getDetailPaymentByARKey(array_column($rs,'pkey'),$criteria);    
        return $this->reindexDetailCollections($rsAllDetail,$indexField);
    }
     
    function getAvailablePaymentVoucherDetail($rs,$includeUsed = false){
        
        $pkey = $rs[0]['pkey'];
        $customerkey = $rs[0]['customerkey'];
         
        $criteria = ($includeUsed) ? ' or '.$this->tableName.'.pkey in( select cashbankvoucherkey from '.$this->tablePaymentMethod .' where refkey = '. $this->oDbCon->paramString($pkey).') '  : '';
        
        // harusnya gk perlu warehouse, bisa saja 1 bank dipake utk beberapa cabang
        // available voucher
        
        
        $rs = $this->searchDataRow( array($this->tableName.'.pkey', $this->tableName.'.code', $this->tableName.'.outstanding'),
                                   ' and ('.$this->tableName.'.customerkey = 0 or '.$this->tableName.'.customerkey in('. $this->oDbCon->paramString($customerkey,',').')) 
                                     and '.$this->tableName.'.outstanding > 0 and '.$this->tableName.'.statuskey = ' .TRANSACTION_STATUS['konfirmasi'] .
                                    $criteria
                );
 
        return $rs;
        
    }
    
    
    function checkARDiscountApproval($pkey){
		$useARDiscountApproval = $this->loadSetting('useARDiscountApproval');
		if (!$useARDiscountApproval) return array();
        
		$arDiscountApproval = new ARDiscountApproval();
		$rsCreditNote = $arDiscountApproval->searchDataRow( array($arDiscountApproval->tableName.'.pkey') ,
                                                     '  and '.$arDiscountApproval->tableName.'.refkey = '.$this->oDbCon->paramString($pkey).' and '.$arDiscountApproval->tableName.'.statuskey in (1,2,3)'
                                                    );
		return $rsCreditNote;
	}
	

    function updateARDiscountApproval($pkey){
		$useARDiscountApproval = $this->loadSetting('useARDiscountApproval');
		if (!$useARDiscountApproval) return;
        
        $rsHeader = $this->getDataRowById($pkey); 
        if($rsHeader[0]['statuskey'] <> 1) return; // cuma update sebelum proses konfirmasi
            
		$pkey = $rsHeader[0]['pkey'];
		$rate = ($rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;
        
        $arDiscountApproval = new ARDiscountApproval();
        
        $totalARDiscountApproval = 0;
        
        // ambil semua detail cost utk dicompare
        $rsCost = $this->getCostDetail($pkey); 
        $headerCost =array(); 
        for($i=0;$i<count($rsCost);$i++){
            array_push($headerCost,$rsCost[$i]['amount']); 
            array_push($headerCost,$rsCost[$i]['costkey']); 
            $totalARDiscountApproval += ($rsCost[$i]['amount'] * $rate);
        }  
 
        // ambil semua detail AR utk dicompare
        $headerDetail =array();
        $rsDetail = $this->getDetailWithRelatedInformation($pkey,' and discount > 0'); 
        for($i=0;$i<count($rsDetail);$i++){
            array_push($headerDetail,$rsDetail[$i]['discount']); 
            array_push($headerDetail,$rsDetail[$i]['arkey']);
            $totalARDiscountApproval += ($rsDetail[$i]['discount'] * $rate);
        }  
 
        $arPaymentHashKey = md5(json_encode($headerCost)).md5(json_encode($headerDetail));

        // cek AR Credit Note
        $rsCreditNote = $this->checkARDiscountApproval($pkey);
        $rsCreditCost = (!empty($rsCreditNote)) ? $arDiscountApproval->getCostDetail($rsCreditNote[0]['pkey']) : array(); 
        $creditCost = array();
        for($i=0;$i<count($rsCreditCost);$i++){
            array_push($creditCost,$rsCreditCost[$i]['amount']); 
            array_push($creditCost,$rsCreditCost[$i]['costkey']);      
        }
 
        $rsCreditDetail = (!empty($rsCreditNote)) ? $arDiscountApproval->getDetailById($rsCreditNote[0]['pkey']) : array(); 
        $creditDetail = array();
        for($i=0;$i<count($rsCreditDetail);$i++){
            array_push($creditDetail,$rsCreditDetail[$i]['discount']); 
            array_push($creditDetail,$rsCreditDetail[$i]['arkey']);      
        }
        
        $arDiscountApprovalHashKey = md5(json_encode($creditCost)).md5(json_encode($creditDetail)); 
     
        if($arPaymentHashKey <> $arDiscountApprovalHashKey){      
            $this->cancelCreditNote($pkey);  
            
            $thresholdDiscount = abs($this->loadSetting('arDiscountApprovalThreshold'));
            
            // kalo lebih besar dr limit, baru perlu approval
            if($totalARDiscountApproval > $thresholdDiscount)
                $this->addCreditNote($rsHeader,$rsDetail,$rsCost);   
        } 
        
    }
    
	function cancelCreditNote($pkey){
		// delete cash out
		$arDiscountApproval = new ARDiscountApproval();

		$rsCredit = $this->checkARDiscountApproval($pkey);
 
		for($i=0;$i<count($rsCredit);$i++) {  
			$arrayToJs = $arDiscountApproval->changeStatus($rsCredit[$i]['pkey'],4,'',false,true);
			if (!$arrayToJs[0]['valid'])
				throw new Exception($arrayToJs[0]['message']);    
		}

	 }
	
	
	function addCreditNote($rsHeader,$rsDetail,$rsCost){
        if (empty($rsDetail) && empty($rsCost))
            return;
        
        $arDiscountApproval = new ARDiscountApproval();
        $warehouse = new Warehouse();
        $item = new Item();
    
        $arr = array();
        $totalCashOut = 0; 
		
		if(!empty($rsCost)){
			for($i=0;$i<count($rsCost);$i++){ 
				$arr['hidDetailCostKey'][$i] = 0;
				$arr['hidCostARDetailKey'][$i] = $rsCost[$i]['pkey'];
				$arr['hidCostKey'][$i] = $rsCost[$i]['costkey'];
				$arr['costAmount'][$i] = $rsCost[$i]['amount'];
			}
			
		}
        
		if(!empty($rsDetail)){
			for($i=0;$i<count($rsDetail);$i++){ 
				$arr['hidDetailKey'][$i] = 0;
				$arr['hidARKey'][$i] = $rsDetail[$i]['arkey'];
				$arr['hidARPaymentDetailKey'][$i] = $rsDetail[$i]['pkey'];
				$arr['outstanding'][$i] = $rsDetail[$i]['outstanding'];
				$arr['discount'][$i] = $rsDetail[$i]['discount']; 
			}
		}
        
         
        $arr['code'] = 'xxxxxx';
        $arr['hidARPaymentKey'] = $rsHeader[0]['pkey'];
        //$arr['refCode'] = $rsHeader[0]['code'];
        $arr['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arr['hidCustomerKey'] = $rsHeader[0]['customerkey'];
        $arr['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        $arr['selCurrency'] = $rsHeader[0]['currencykey'];
        $arr['trDesc'] = $rsHeader[0]['trdesc'];
        $arr['islinked'] = 1; 
             
        $arrayToJs = $arDiscountApproval->addData($arr); 

        if (!$arrayToJs[0]['valid'])
            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);
        
    }
    
    
    function generateCashflowReport($criteria=''){
		$rsTrans = array(); 
		$arrSQL = array(); 
					
		$sql = 'SELECT  
					warehousekey,
					currencykey,  
					Year(trdate) as tryear,
					month(trdate) as trmonth,
					CONCAT(MONTHNAME(trdate), \' \' ,YEAR(trdate)) AS trmonthyear,
					CONCAT(YEAR(trdate),\'-\',MONTH(trdate)) AS timeindex,
					SUM(totalpayment * rate) AS totalidr, 
					'.$this->tableCurrency.'.name as currencyname
				FROM '.$this->tableName.' 
					 left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey 
				where 
					'.$this->tableName.'.statuskey in(2,3) ';
		
		if (!empty($criteria)) $sql .=  ' ' .$criteria;   
		 
		$sql .= ' GROUP BY warehousekey,currencykey,timeindex'; 
		$sql .= ' ORDER BY trdate asc';
         
       return $this->oDbCon->doQuery($sql);
		 
    }	


    function getAttachmentToPrint($id)
    {

        $arrReturn = array();

        $rsItemFile = $this->getItemFile($id);

        foreach ($rsItemFile as $fileRow) {
            array_push($arrReturn, $this->defaultDocUploadPath . $this->uploadFileFolder . $fileRow['refkey'] . '/' . $fileRow['file']);
        }

        return $arrReturn;
    }
    
    function getItemFile($pkey)
    {
        $sql = 'select * from ' . $this->tableFile . ' where refkey = ' . $this->oDbCon->paramString($pkey) . ' order by pkey asc';
        return $this->oDbCon->doQuery($sql);
    }

    function updateFile($pkey, $token, $arrFile)
    {

        if (!empty($arrFile))
            $this->validateDiskUsage();

        $sourcePath = $this->uploadTempDoc . $this->uploadFileFolder . $token;
        $destinationPath = $this->defaultDocUploadPath . $this->uploadFileFolder;


        if (!is_dir($destinationPath))
            mkdir($destinationPath, 0755, true);

        $destinationPath .= $pkey;


        //delete previous files	    
        $this->deleteAll($destinationPath);
        $sql = 'delete from ' . $this->tableFile . ' where refkey = ' . $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);

        if (!is_dir($sourcePath))
            return;

        
        if (!empty($arrFile)) {

            $arrFile = explode(",", $arrFile);
            for ($i = 0; $i < count($arrFile); $i++) {
                $this->uploadImage($sourcePath, $destinationPath, $arrFile[$i]);

                $imagekey = $this->getNextKey($this->tableFile);
        
                $sql = 'insert into ' . $this->tableFile . ' (pkey,refkey,file) values (' . $this->oDbCon->paramString($imagekey) . ',' . $this->oDbCon->paramString($pkey) . ',' . $this->oDbCon->paramString($arrFile[$i]) . ')';
                $this->oDbCon->execute($sql);

            }
        }

    }

    function updateDocumentFiles($pkey, $fieldName, $arrFile)
    {

        $arrayToJs = array();

        try {

            if (!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]);

            $rsHeader = $this->getDataRowById($pkey);

            if ($rsHeader[0]['statuskey'] == 2) { // khusus kalo status "konfirmasi", TCO ad 5 status
                $this->updateFile($pkey, $arrFile[0]['token'], implode(",", array_column($arrFile, 'fileName')));
            }


            $this->oDbCon->endTrans();
            $this->addErrorList($arrayToJs, true, $this->lang['dataHasBeenSuccessfullyUpdated']);

        } catch (Exception $e) {
            $this->oDbCon->rollback();
            $this->addErrorList($arrayToJs, false, $e->getMessage());
        }

        return $arrayToJs;
    }
    
    
	function getTransactionDescription($arrKey,$userkey= ''){
                     
        $arrAvailableField = array(  
                        array('tableName' => $this->tableName, 'param' => 'CUSTOMER_NAME', 'tableReference' => array('tableName' => $this->tableCustomer,  
																													 'field' => $this->tableCustomer.'.name',
																													 'refkey' => 'customerkey'
																													)), 
                        array('tableName' => $this->tableName, 'param' => 'DESCRIPTION', 'field' => $this->tableName.'.trnotes'),    
        );
		
        return $this->stitchDescriptionV2(array('field' => $arrAvailableField, 'pkey' => $arrKey, 'userkey' => $userkey ));
	 }
	
}
?>
