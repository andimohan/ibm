<?php

class APPayment extends BaseClass{  
  
    function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ap_payment_header';
		$this->tableNameDetail = 'ap_payment_detail';
		$this->tableSupplier = 'supplier';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tablePayment= 'ap_payment';
		$this->tableCost= 'ap_cost';
        $this->tableItem = 'cost_cash_out';
	    $this->tableJobOrder= 'trucking_service_order_header';
        $this->tableDownpaymentDetail = 'ap_downpayment';
        $this->tableDownpayment = 'supplier_downpayment';
	    $this->tableAP = 'ap';
		$this->tableAPType = 'ap_type';
        $this->tableCurrency = 'currency';
        $this->tablePaymentMethod = 'payment_method';
        $this->tableCashBank = 'cash_bank';
        $this->tableFile = 'ap_payment_file';
		$this->uploadFileFolder = 'ap-payment/';  
        $this->useStorage = $this->useStorage('S3');		  
       
        $this->isTransaction = true;
		 
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tablePayment, $this->tableDownpaymentDetail,$this->tableCost);
		
		$this->securityObject = 'APPayment';
        
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['apkey'] = array('hidAPKey');
        $this->arrDataDetail['outstanding'] = array('outstanding','number');
        $this->arrDataDetail['amount'] = array('amount', array('datatype' => 'number','mandatory'=>true));
        $this->arrDataDetail['discount'] = array('discount','number');
        $this->arrDataDetail['taxamount'] = array('taxPPH','number');
        $this->arrDataDetail['pphtype'] = array('selPPhType');
       
        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); 
        $arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');
       
       
        $arrDownpaymentDetail = array(); 
        $arrDownpaymentDetail['pkey'] = array('hidDetailDownpaymentKey');
        $arrDownpaymentDetail['refkey'] = array('pkey', 'ref');
        $arrDownpaymentDetail['amount'] = array('downpaymentAmount',array('datatype' => 'number','mandatory'=>true));
        $arrDownpaymentDetail['downpaymentkey'] = array('hidDownpaymentKey',array('mandatory'=>true)); 
        

  	    $arrCostDetail = array(); 
        $arrCostDetail['pkey'] = array('hidDetailCostKey');
        $arrCostDetail['refkey'] = array('pkey', 'ref');
        $arrCostDetail['amount'] = array('costAmount',array('datatype' => 'number','mandatory'=>true));
        $arrCostDetail['costkey'] = array('hidCostKey',array('mandatory'=>true)); 
 
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment));
        array_push($arrDetails, array('dataset' => $arrDownpaymentDetail, 'tableName' => $this->tableDownpaymentDetail));
        array_push($arrDetails, array('dataset' => $arrCostDetail, 'tableName' => $this->tableCost));
        
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
        $this->arrData['ntpn'] = array('ntpn');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['nettingkey'] = array('nettingkey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['supplierkey'] = array('hidSupplierKey');	
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
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['rate'] = array('currencyRate','number');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['taxobjectcode'] = array('taxObjectCode'); 
        $this->arrData['taxperiod'] = array('taxPeriod','date'); 
        $this->arrData['salesordercodecache'] = array('salesordercodecache'); 
//		$this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'tableName' => $this->tableFile, 
//																  'uploadFolder' => $this->uploadFileFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
//    
//          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        
		// untuk yg di form, narik AP utk pembyrna JO apa
        if(in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'])))
            array_push($this->arrDataListAvailableColumn, array('code' => 'JOCode','title' => 'JOCode','dbfield' => 'refordercode','default'=>true, 'width' => 100));
         
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname', 'default'=>true, 'width' => 60,  'align' =>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalpaid','title' => 'payingOffAmount','dbfield' => 'totalpaid', 'default'=>true, 'width' => 120,  'align' =>'right',  'format' => 'number' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'tax23','title' => 'tax23','dbfield' => 'payabletax23', 'default'=>true, 'width' => 100, 'align' =>'right',  'format' => 'number' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'refCode','dbfield' => 'refcode', 'width' => 100));    
 
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/apPayment'));
        array_push($this->printMenu,array('code' => 'printTransactionByCategory', 'name' => $this->lang['printSummary'],  'icon' => 'print', 'url' => 'print/apPaymentByCategory'));
        
        $this->includeClassDependencies(array(
                  'AP.class.php', 
                  'APPayableTax23.class.php', 
                  'APPayableTax23Payment.class.php', 
                  'CashBank.class.php', 
                  'ChartOfAccount.class.php', 
                  'COALink.class.php', 
                  'Currency.class.php', 
                  'CostCashOut.class.php', 
                  'Downpayment.class.php',
                  'SupplierDownpayment.class.php',  
                  'EMKLPurchaseOrder.class.php', 
                  'EMKLJobOrder.class.php', 
                  'EMKLCommission.class.php', 
                  'GeneralJournal.class.php', 
                  'Supplier.class.php', 
                  'Service.class.php', 
                  'TruckingServiceOrder.class.php', 
                  'TruckingServiceWorkOrder.class.php', 
                  'TruckingServiceOrderInvoice.class.php', 
                  'PurchaseOrder.class.php', 
                  'Warehouse.class.php', 
                  'CarServiceMaintenance.class.php',
                  'DisposalPurchaseOrder.class.php',
                  'TruckingPurchase.class.php',
                  'Tax.class.php'
        
        ));  

        $this->overwriteConfig();
	}
	
    function getQuery(){
		  if(PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking']){ 
			$sql = '
				SELECT '.$this->tableName.'.* ,
				   '.$this->tableSupplier.'.name as suppliername,
				   '.$this->tableWarehouse.'.name as warehousename,
				   '.$this->tableSupplier.'.taxid as suppliertaxid,
				   '.$this->tableJobOrder.'.code as refordercode,
				   '.$this->tableStatus.'.status as statusname,              
				   '.$this->tableCurrency.'.name as currencyname
				FROM 
					'.$this->tableName.' left join '.$this->tableJobOrder.' 
						on  '.$this->tableName.'.refkey = '.$this->tableJobOrder.'.pkey, 
					'.$this->tableStatus.', 
					'.$this->tableSupplier.', 
					'.$this->tableWarehouse.',
					'.$this->tableCurrency.'
				WHERE '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and
					  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey   and
					  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey ';

		  }else{
			  $sql = '
				SELECT '.$this->tableName.'.* ,
				   '.$this->tableSupplier.'.name as suppliername,
				   '.$this->tableWarehouse.'.name as warehousename,
				   '.$this->tableSupplier.'.taxid as suppliertaxid, 
				   '.$this->tableStatus.'.status as statusname,              
				   '.$this->tableCurrency.'.name as currencyname
				FROM  '.$this->tableName.' 
                        left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey ,
					'.$this->tableStatus.',
					'.$this->tableWarehouse.',
					'.$this->tableCurrency.'
				WHERE  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey   and
					  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey ';
		  }
		
		$sql .= ' ' .$this->criteria ; 
        $sql .=  $this->getWarehouseCriteria() ;
        
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
                   
				    //if (!empty($arrPph[$i]) && $arrPph[$i]>0)  // agar support php23 negativ yg dibalikin (yg sudah terlanjur dipotong - logol) 
                    if (!empty($arrPph[$i]))   
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
        $downpayment = new SupplierDownpayment();
        $truckingServiceOrder = new TruckingServiceOrder();
        
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$supplierkey = $arr['hidSupplierKey'];  
		$arrAPkey = $arr['hidAPKey']; 
		$arrAmount = $arr['amount'];
		$arrOutstanding= $arr['outstanding'];
		$arrDiscount = $arr['discount'];
        $arrDownpaymentKey = $arr['hidDownpaymentKey'] ?? [];
		$arrDownpaymentAmount = $arr['downpaymentAmount'];
		$arrDownpaymentCode = $arr['downpaymentCode'];
		$refkey = $arr['hidRefKey'];
        $grandtotal = $arr['grandtotal'];
        $trDate = $arr['trDate'];
		$currencykey = $arr['selCurrency']; 
		//$arrPick = $arr['chkPick']; 

        $paymentStrictToWarehouse = $this->loadSetting('APPaymentStrictWarehouse');
            
        $arrDetailKey = array(); 
         
        $rsAP = (!empty($arrAPkey)) ? $APObj->searchData('','',true, ' and '.$APObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrAPkey)).') ') : array(); 
        
        $arrAP = array_column($rsAP, null, 'pkey');
        $arrAPSupplier = array_column($rsAP, 'supplierkey', 'pkey');
        $arrAPRefkey2 = array_column($rsAP, 'refkey2', 'pkey');
        $arrDate = array_column($rsAP, 'trdate', 'pkey');
         
        //khusus trucking
        $refCode = '';
        if(!empty($refkey)) {
            $rsJO =  $truckingServiceOrder->getDataRowById($refkey);
            $refCode = $rsJO[0]['code'];
        } 
        
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
			
		if(empty($supplierkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
		}
		   
        $hasAP = false; 
		for($i=0;$i<count($arrAPkey);$i++) { 
			if (!empty($arrAPkey[$i]))  //  && !empty($arrPick[$i])
                $hasAP = true;  
            
            if (in_array($arrAPkey[$i],$arrDetailKey)){   
                $this->addErrorList($arrayToJs,false, $arrAP[$arrAPkey[$i]]['code'].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKey, $arrAPkey[$i]);
            }
            
        }
        
        if (!$hasAP)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ap'][1]); 	
         
//        if ($grandtotal < 0)
//            $this->addErrorList($arrayToJs,false, $this->errorMsg['apPayment'][3]); 	
            
		for($i=0;$i<count($arrAPkey);$i++) {  
            if(!empty($arrAPkey[$i])){
                
                $outstanding = $this->unFormatNumber($arrOutstanding[$i]);
                $amount = $this->unFormatNumber($arrAmount[$i]); 
                $discount = $this->unFormatNumber($arrDiscount[$i]);
                
                if ( $amount == 0 || 
                    ($outstanding > 0 && $amount < 0) || 
                    ($outstanding > 0 && ($amount+$discount) > $outstanding) || //overpay
                    ($outstanding < 0 && (($amount+$discount) < $outstanding ||  $amount > 0)) //overpay
                   ) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]]['code']. '</strong>. ' . $this->errorMsg['apPayment'][2]); 
               
                if ($arrAPSupplier[$arrAPkey[$i]] <> $supplierkey) 
                    $this->addErrorList($arrayToJs,false,$arrAP[$arrAPkey[$i]]['code']. '. ' . $this->errorMsg['ap'][5]); 
				
                if($paymentStrictToWarehouse == 1){
                    if($arr['selWarehouseKey']<>$arrAP[$arrAPkey[$i]]['warehousekey'])
                        $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]]['code'].'</strong>. '.$this->errorMsg[905]); 
                }
                
				 if($currencykey<>$arrAP[$arrAPkey[$i]]['currencykey'])
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]]['code'].'</strong>. '.$this->errorMsg['apPayment'][5]); 
                
                //validasi kalo isi JO sebagai referensi, pastikan semua hutangnya mengacu ke SPK dengan JO yg sama
                if(!empty($refkey) && $arrAPRefkey2[$arrAPkey[$i]] <> $refkey){
                    $this->addErrorList($arrayToJs,false,$arrAP[$arrAPkey[$i]]['code']. '. ' . $this->errorMsg['ap'][7] .' ('.$refCode.')'); 
                }
                 
                
                $apDate = $this->formatDBDate($arrDate[$arrAPkey[$i]],'d / m / Y');
                $dateDiff = $this->dateDiff($trDate,$apDate);  
                if($dateDiff > 0)
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]]['code'].'</strong>.'. $this->errorMsg['apPayment'][4]);
                
                
            } 
		}
 
        
        $arrDownpaymentExistKey = array(); 
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
    
//    function afterUpdateData($arrParam, $action)
//    {
//        // hanya boleh kalo statusnya blm selesai
//        $rsHeader = $this->getDataRowById($arrParam['pkey']);
//        if ($rsHeader[0]['statuskey'] < 3)
//            $this->updateFile($arrParam['pkey'], $arrParam['token-item-file-uploader'], $arrParam['item-file-uploader']);
//    }
	
    function afterStatusChanged($rsHeader){ 
        
		$APObj = $this->getAPObj();
         
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
  
        for($i=0;$i<count($rsDetail); $i++){   
           $APObj->updateAPOutstanding($rsDetail[$i]['apkey']); 
        }   
           
        $supplierDownpayment = new SupplierDownpayment();
        $rsDownpayment = $this->getDownpaymentDetail($rsHeader[0]['pkey'],'',false);
			
        for($i=0;$i<count($rsDownpayment); $i++){  
           $supplierDownpayment->updateOutstanding($rsDownpayment[$i]['downpaymentkey']); 
        }
        
        // retrieve latest status
//        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
//        if ($rsHeader[0]['statuskey'] == 2)
//            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
	  
	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $supplierkey =  $rsHeader[0]['supplierkey'];
		$currencykey =  $rsHeader[0]['currencykey'];
        $rate = ($rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;
        
		$coaLink = new COALink();
        $warehouse = new Warehouse();  
        $ap = $this->getAPObj();
         
        $rsPayment = (ADV_FINANCE && TEST_VOUCHER) ?  $this->getPaymentVoucherDetail($id,'',2) : $this->getPaymentMethodDetail($id);  
        $rsDownpayment = $this->getDownpaymentDetail($id,'',false);
         
        $isnetting = (isset($rsHeader[0]['nettingkey']) && !empty($rsHeader[0]['nettingkey'])) ? true : false;
        
        if($isnetting){
            $totalPayment = $rsHeader[0]['grandtotal'];
        }else{  
            $totalPayment = 0; 
            for($i=0;$i<count($rsPayment); $i++)
                $totalPayment += $rsPayment[$i]['amount'];
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
            array_push($arrCOA, 'ap' , 'otherrevenue', 'othercost','payabletax23','supplierdownpayment'); 
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
                    // cek kalo supplierkey sudah beda
                    if ($rsPayment[$i]['vouchersupplierkey'] <> $supplierkey)
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
        $arrKeys = array_column($rsDetail,'apkey');      
        $rsAP = $ap->searchData('','',true,' and ' .$ap->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') ' ); 
        
        
        // cek status hutang sudah lunas atau blm
        if (!empty($arrKeys)){ 
            $rsAPPaid = $ap->searchData('','',true,' and ' .$ap->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') and ' .$ap->tableName.'.statuskey in (3,4) ' );
            if (!empty($rsAPPaid)){
                $arrAP = array_column($rsAPPaid,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br><strong>'.implode(', ',$arrAP).'</strong>. '.$this->errorMsg['ap'][6]); 
            }
        }
        
        $trDate =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        for($i=0;$i<count($rsAP);$i++){
            $apDate = $this->formatDBDate($rsAP[$i]['trdate'],'d / m / Y');
            $dateDiff = $this->dateDiff($trDate,$apDate);
            if($dateDiff > 0)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['apPayment'][4]); 

	    if($currencykey<>$rsAP[$i]['currencykey'])
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$rsAP[$i]['code'].'</strong>. '.$this->errorMsg['apPayment'][5]); 
        }
 
        for($i=0;$i<count($rsDownpayment);$i++) {   
            
            // validasi DP masi available gk 
            if($rsDownpayment[$i]['downpaymentstatuskey'] <> 2){ 
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][9]);
            }else{
                if ( $supplierkey <> $rsDownpayment[$i]['downpaymentsupplierkey'])
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][7]); 


				if ($currencykey <> $rsDownpayment[$i]['downpaymentcurrencykey'])
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][10]); 
                // validasi nilai DP masi mencukupi gk 
                if ($rsDownpayment[$i]['amount'] > $rsDownpayment[$i]['downpaymentoutstanding'] )
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDownpayment[$i]['downpaymentoutstanding']).')');  
            }
                
        }
        
         // cek setiap detail ad g overpaid gk
        // harus ambil ulang outstanding AP
        $rsAP = array_column($rsAP,null,'pkey');
        foreach($rsDetail as $detailRow){
           $apRow = $rsAP[$detailRow['apkey']];
             
           if ( ($apRow['outstanding'] > 0 && ($detailRow['amount']+$detailRow['discount']) > ($apRow['outstanding']+1) ) || //overpay
               ($apRow['outstanding']  < 0 && (($detailRow['amount']+$detailRow['discount']) < ($apRow['outstanding']-1)  ||  $detailRow['amount'] > 0)) //overpay
              )   
            $this->addErrorLog(false,'<strong>'.$apRow['code']. '</strong>. ' . $this->errorMsg['apPayment'][2]); 
           
        }
 	 }
    

    // gk ad bedanya sama baseclass
//  function getPaymentVoucherDetail($pkey,$paymentMethodKey='', $isSupplier = false){
//        
//        $fieldName = ($isSupplier) ?  $this->tableCashBank.'.supplierkey as vouchersupplierkey'  :  $this->tableCashBank.'.customerkey as vouchercustomerkey';
//        
//        $paymentMethodKeyCriteria = '';
//        if (!empty($paymentMethodKey))
//            $paymentMethodKeyCriteria = ' and cashbankvoucherkey = ' . $this->oDbCon->paramString($paymentMethodKey);
//
//        $sql = 'select 
//                    '.$this->tablePayment.'.*,
//                    '.$this->tableCashBank.'.code as vouchercode,
//                    '.$this->tableCashBank.'.outstanding as voucheroutstanding,
//                    '.$this->tableCashBank.'.amount as voucheramount,
//                    '.$fieldName.',
//                    '.$this->tableCashBank.'.statuskey as voucherstatuskey
//                from  
//                   '.$this->tablePayment.',
//                   '.$this->tableCashBank.' 
//                where 
//                   '.$this->tablePayment.'.cashbankvoucherkey = '.$this->tableCashBank.'.pkey and  
//                    '.$this->tablePayment.'.refkey = '.$this->oDbCon->paramString($pkey).$paymentMethodKeyCriteria.' order by cashbankvoucherkey asc';	
//        
//        $rs = $this->oDbCon->doQuery($sql); 
//        
//        $totalRs = count($rs);
//        for($i=0;$i<$totalRs;$i++)
//            $rs[$i]['voucherlabel'] = $this->formatNumber($rs[$i]['voucheroutstanding']). ' ('.$rs[$i]['vouchercode'].')';
//            
//        return $rs;
//    }
	
	
	function confirmTrans($rsHeader){
        $id = $rsHeader[0]['pkey'];
         
        $coaLink = new COALink(); 
		$warehouse = new Warehouse();  
		$supplier = $this->getBussinessPartnerObj();
		//$cashMovement = new CashMovement();  
        $cashBank = new CashBank(); 
        
		$rsSupplier = $supplier->getDataRowById($rsHeader[0]['supplierkey']);
		$notecash = $rsHeader[0]['code'].'. Kas Keluar untuk pembayaran hutang dari '.$rsSupplier[0]['name'];
		$rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
		 
		// MENGHITUNG PAYMENT
        if (ADV_FINANCE && TEST_VOUCHER){ 
            $rsPayment = $this->getPaymentVoucherDetail($id,'',2);
            $rsAPKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
            // update outstanding voucher  
            foreach($rsPayment as $voucherlist){ 
                $cashBank->insertTransaction(
                    array('refkey' => $voucherlist['cashbankvoucherkey'],
                          'reftablekey' => $rsAPKey['key'],
                          'reftranskey' => $rsHeader[0]['pkey'],
                          'refcode' => $rsHeader[0]['code'],
                          'refdate' => $rsHeader[0]['trdate'],
                          'amount' => $voucherlist['amount'],
                ),true
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


					//$cashMovement->updateCashMovement($id, $coakey,-$rsPayment[$i]['amount'], $this->tableName, $rsHeader[0]['warehousekey'], $notecash,$rsHeader[0]['trdate']);
                    $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'],'coakey' => $coakey, 'amount' => -$rsPayment[$i]['amount'])); 
                    $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey']; 
				}
           
		}           
        }
				// END
        
        
		//update jurnal umum 
        $this->updateGL($rsHeader,$rsPayment);
        
        if ($rsHeader[0]['payabletax23'] != 0) 
            $this->updateAPPrepaid($rsHeader,$rsDetail); 
	} 
    
    function updateAPPrepaid($rsHeader,$rsDetail){
        
            $apPayableTax23 = $this->getPayableTaxObj();  
            $ap = $this->getAPObj();
            
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
                
                if ($rsDetail[$i]['taxamount'] == 0) continue;
                    
                $arrParam = array();
                 
                $rsAP = $ap->getDataRowById($rsDetail[$i]['apkey']); 
                   
                // kalo add manual, gk ad obj
                $rsPO = array();
                if (!empty($rsAP[0]['reftabletype'])){
                        // cek tipe AP    
//                        $type = $this->getTableNameAndObjById($rsAP[0]['reftabletype']);
                    
                          $poObj = $this->getObjMapping('',$rsAP[0]['reftabletype']); 
                    
                          // cek ulang ad tablecost gk
                          if(empty($poObj)){ 
                                $truckingServiceWorkOrder = new TruckingServiceWorkOrder(); 
                                $tableName = $this->getTableNameAndObjById($rsAP[0]['reftabletype'],array('tableName'))['tableName'];
                                if ($tableName == $truckingServiceWorkOrder->tableCost ){
                                    $purchaseObj = $truckingServiceWorkOrder;
                                }
                              
                          }else{
                               $purchaseObj = $poObj;  
                          }
                    
//                        switch($poObj->tableName){
//                            case $truckingServiceWorkOrder->tableCost: $purchaseObj = $truckingServiceWorkOrder;
//                                                                        break;
//                            default : 
//                                    $purchaseObj = $poObj;  
//                        }
					
                        $rsPO = $purchaseObj->getDataRowById($rsAP[0]['refheaderkey']);
                }
                
           
                
                $rsAPKey =  $ap->getTableKeyAndObj($this->tableName);                  
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidSupplierKey'] = $rsHeader[0]['supplierkey']; 
                $arrParam['hidRefKey'] = $rsDetail[$i]['pkey']; //$rsPO[0]['pkey']; // kepake gk ??? harusnya ambil detailkey dr payment
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  (!empty($rsPO)) ? $rsPO[0]['code'] : '';
                $arrParam['hidRefDate'] =  (!empty($rsPO)) ? $this->formatDBDate($rsPO[0]['trdate'],'d / m / Y') : DEFAULT_EMPTY_DATE;  
                $arrParam['hidRefTable'] = $rsAPKey['key'];
                $arrParam['amount'] = $rsDetail[$i]['taxamount'] * $rate;
                $arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['dueDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['createdBy'] = 0; 
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['selWarehouse'] = $rsHeader[0]['warehousekey'];
                $arrParam['selPPhType'] = $pphTypeKey;
                
                $returnVal = $apPayableTax23->addData($arrParam,false);  
 
            }  
    }
	
    function validateCancel($rsHeader, $autoChangeStatus = false){ 
         
        $id = $rsHeader[0]['pkey'];
        
		if ( !$autoChangeStatus ) {
			if(isset($rsHeader[0]['islinked']) && !empty($rsHeader[0]['islinked']))
				$this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[900],true);  
		}  

        if(!$this->validateAutoReverseGL($id))
            $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['generalJournal'][6],true);
        
        $ap = $this->getAPObj();
        $apPayableTax = $this->getPayableTaxObj();
   
        //cek ad Prepaid yg ad bukti potongnya blm 
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName,array('key'));                  
		$rsAP = $apPayableTax->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and '.$apPayableTax->tableName.'.reftabletype = '.$rsAPKey['key'].' and ('.$apPayableTax->tableName.'.statuskey in (2,3) )');
     
		if(!empty($rsAP)) {
            $arrAP = array_column($rsAP,'code');
			$this->addErrorLog( false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' Bukti bayar sudah diinput.<br>' . implode(', ', $arrAP ).'.');
        }
        
	 } 
 
	function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey'];
		//$cashMovement = new CashMovement();    
		//$cashMovement->cancelMovement($id,$this->tableName);
        $rsAPKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
        
		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			if (ADV_FINANCE && TEST_VOUCHER){ 
				$cashBank->removeTransaction($id,$rsAPKey['key']);
			}else{ 
				$cashBank->cancelCashBank($rsHeader,$this->tableName);
			}
		}

        $this->deleteAPPrepaidTax($id); 
        
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
        $supplier = new Supplier();
        $cashBank = new CashBank();
        $costCashOut = new CostCashOut();
        $chartOfAccount = new ChartOfAccount();
        
        $paymentCurrencyKey = $rs[0]['currencykey'];
        
		$multiCurrency = ($rs[0]['currencykey'] != CURRENCY['idr']) ? true : false; // khusus currency selain IDR
        $totalPaymentAmount = 0;
		
        $warehousekey = $rs[0]['warehousekey'];
        $rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;
		$totalDiscount = $rs[0]['totaldiscount'] * $rate;
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0;  
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
        // desc
        $desc = array(); 
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        array_push($desc,html_entity_decode($rsSupplier[0]['name'])); 
        if(!empty($rs[0]['trnotes'])) array_push($desc,$rs[0]['trnotes']);
		$arr['trDesc'] = implode(chr(13),$desc);  
		
		$temp = -1; 
        // khusus logol
		if(ADV_FINANCE && TEST_VOUCHER) 
			$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);

        for($i=0;$i<count($rsPayment); $i++){ 
            
            if(ADV_FINANCE && TEST_VOUCHER){ 
//				$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2); // harusnya udah gk perlu
				$rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
				$rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);
                
				$paymentcoakey = $rsCOA[0]['countercoakey'];
                $paymentRate = $rsCashBank[0]['rate'];  
			}else{
				$rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
				$paymentcoakey = $rsCOA[0]['coakey'];
                $paymentRate = $rs[0]['rate']; //  pake header rate, seperti diawal sebelum pake modul voucher
			}
            
			 $paymentAmount = $rsPayment[$i]['amount'] * $paymentRate; 
             
             $temp++;
             $arr['hidCOAKey'][$temp] = $paymentcoakey;
             $arr['debit'][$temp] = 0; 
             $arr['credit'][$temp] = $paymentAmount; 
             $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
             $arr['debitSource'][$temp] = 0; 
             $arr['creditSource'][$temp] = $rsPayment[$i]['amount']; 
             $arr['rate'][$temp] = $paymentRate ; 
             $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  
			 $totalPaymentAmount += $paymentAmount;
        }
        		
        $rsDownpayment = $this->getDownpaymentDetail($rs[0]['pkey'],'',false);  
        for($i=0;$i<count($rsDownpayment); $i++){  
            
             $dwnpaymentRate = $rsDownpayment[$i]['downpaymentrate'];
			 $downpaymentAmount = $rsDownpayment[$i]['amount'] * $dwnpaymentRate;
             $temp++;
             $arr['hidCOAKey'][$temp] = $supplier->getDownpaymentCOAKey($rs[0]['supplierkey'],$warehousekey);   
             $arr['debit'][$temp] = 0; 
             $arr['credit'][$temp] = $downpaymentAmount; 
             $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
             $arr['debitSource'][$temp] = 0; 
             $arr['creditSource'][$temp] = $rsDownpayment[$i]['amount'] ; 
             $arr['rate'][$temp] = $dwnpaymentRate;
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
                         
            // berbeda dengan AR, harus dipotong
			 $totalPaymentAmount -= $costAmount;
        }
        
         $tax23Amount = $rs[0]['payabletax23'] * $rate;
		 $totalPaymentAmount += $tax23Amount;
        
        
         //$rsCOA = $coaLink->getCOALink ('payabletax23', $warehouse->tableName,$warehousekey,0); 
         //$temp++;
         //$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
         //$arr['debit'][$temp] = 0; 
         //$arr['credit'][$temp] = $tax23Amount;  
         //$arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
         //$arr['debitSource'][$temp] = 0; 
         //$arr['creditSource'][$temp] = $rs[0]['payabletax23']; 
         //$arr['rate'][$temp] = $rate ; 
         //$arr['refCashBankKey'][$temp] = '';  
                        
                        
        $arrPPHTypeKey = array();
        $rsDetail = $this->getDetailById($rs[0]['pkey']);
        $arrPPHTypeKey = array_column($rsDetail, 'pphtype' );
        
        
        //PPH
        if($rs[0]['payabletax23'] != 0){
            $tax = new Tax();
            $rsCOA = $tax->getPPhCOA($arrPPHTypeKey, $rs[0]['warehousekey'],false);
            $rsCOACols = array_column($rsCOA,null,'pkey');
           
            $arrTaxAmount = array();
            for($i=0;$i<count($rsDetail);$i++){ 
                $pphTypeKey = (isset($rsDetail[$i]['pphtype'])) ? $rsDetail[$i]['pphtype'] : 0 ;  // defaultnya 0 kalo gk ad jenis tax
                $taxAmount = $rsDetail[$i]['taxamount']; 
                $taxCOAKey = $rsCOACols[$pphTypeKey]['coakey'];    
                
                //// kalo kosong tembak pph23 default warehouse 
                if(!isset($arrTaxAmount)) $arrTaxAmount[$taxCOAKey] = 0;
                
                $arrTaxAmount[$taxCOAKey] += $taxAmount;
            }
            
            foreach($arrTaxAmount as $taxCOAKey=>$taxAmount){
                $temp++;
                $arr['hidCOAKey'][$temp] = $taxCOAKey; 
                $arr['debit'][$temp] = 0; 
                $arr['credit'][$temp] =  $taxAmount * $rate;  
                $arr['debitSource'][$temp] = 0; 
                $arr['creditSource'][$temp] =  $taxAmount; 
                $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey; 
                $arr['rate'][$temp] = $rate ; 
                $arr['refCashBankKey'][$temp] = '';
            } 
        }
 
 
        $temp++; 
        $rsCOA = $coaLink->getCOALink ('purchaseretaildiscount', $warehouse->tableName,$warehousekey, 0); // ini harus dipisah antara jasa / retail sepertinya
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['totaldiscount']  * $rate; 
        $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
        $arr['debitSource'][$temp] = 0; 
        $arr['creditSource'][$temp] =  $rs[0]['totaldiscount']; 
        $arr['rate'][$temp] = $rate ; 
        $arr['refCashBankKey'][$temp] = '';  
		 
        //selisih pembayaran   
        $temp++; 
        if ($rs[0]['balance'] < 0){ 
            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = abs($rs[0]['balance'])  * $rate; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] =   abs($rs[0]['balance']) ; 
            $arr['refCashBankKey'][$temp] = '';  
        }else{ 
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = abs($rs[0]['balance'])  * $rate; 
            $arr['credit'][$temp] = 0; 
            $arr['debitSource'][$temp] = abs($rs[0]['balance']); 
            $arr['creditSource'][$temp] =   0 ; 
            $arr['refCashBankKey'][$temp] = '';  
        }

        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
        $arr['rate'][$temp] = $rate ; 
		
		$ap = $this->getAPObj();
		$rsDetail = $this->getDetailById($rs[0]['pkey']);
		$totalAP = 0;
		$totalAPSource = 0; // kayanya gk kepake 
        $totalAPByType= array();
        $totalAPSourceByType= array();
        
        $rsAPCol = $ap->searchDataRow(array($ap->tableName.'.pkey', $ap->tableName.'.amountidr', $ap->tableName.'.amount', $ap->tableName.'.rate', $ap->tableName.'.aptype'),
                                      ' and '.$ap->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'apkey'),',').')'
                                     );
         
        $rsAPCol = array_column($rsAPCol,null,'pkey');

		$arrAPGroupByType = array();
        
		foreach($rsDetail as $key=>$rowDetail){ 
            $rsAp = $rsAPCol[$rowDetail['apkey']]; 
			
			$subtotalAp = ($rowDetail['amount'] * $rsAp['rate']);
			$subtotalApSource = $rowDetail['amount'];
			
            $subtotalDiscount = $rowDetail['discount'] * $rate; // potong dengan rate pembayaran agar dpt selisih kurs
			$subtotalDiscountSource = $rowDetail['discount'];
			
            $totalAP += $subtotalAp;
            $totalAPSource += $subtotalApSource;
			
			// group berdasarkan jenisnya, agar counter jurnalnya ketauan
            $apTypeKey = $rsAp['aptype'];
			if((!isset($arrAPGroupByType[$apTypeKey]))){
				
				$coakey = $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey,$apTypeKey);
				
				$arrAPGroupByType[$apTypeKey] = array();
				$arrAPGroupByType[$apTypeKey]['coakey'] = $coakey;
				$arrAPGroupByType[$apTypeKey]['amount'] = 0;
				$arrAPGroupByType[$apTypeKey]['sourceamount'] = 0;
				$arrAPGroupByType[$apTypeKey]['rate'] = $rsAp['rate'];
                
                $totalAPByType[$apTypeKey] = 0;
                $totalAPSourceByType[$apTypeKey] = 0;
			}	

            // kalo gk dipisah, kalo ad DN, jd error karena sum nya 0
            $totalAPByType[$apTypeKey] += $subtotalAp;
            $totalAPSourceByType[$apTypeKey] += $subtotalApSource;
			  
            
			$arrAPGroupByType[$apTypeKey]['amount'] += ($subtotalAp + $subtotalDiscount);
			$arrAPGroupByType[$apTypeKey]['sourceamount'] += ($subtotalApSource + $subtotalDiscountSource);
			$arrAPGroupByType[$apTypeKey]['rate'] = $totalAPByType[$apTypeKey] / $totalAPSourceByType[$apTypeKey]; // hitung ulang rate rata2
        }

		$totalDifference = $totalPaymentAmount - $totalAP;
         
//        $temp++; 
//		$arr['hidCOAKey'][$temp] =  $supplier->getAPCOAKey($rs[0]['supplierkey'],$warehousekey);
//		$arr['debit'][$temp] = $totalAP + $totalDiscount;
//		$arr['credit'][$temp] = 0;
//        $arr['refCashBankKey'][$temp] = '';  
		
		foreach($arrAPGroupByType as $key=>$apTypeRow){
			$temp++; 
			$arr['hidCOAKey'][$temp] = $apTypeRow['coakey'];
			$arr['debit'][$temp] = $apTypeRow['amount']; 
			$arr['credit'][$temp] =0;
            $arr['selCurrencyKey'][$temp] = $paymentCurrencyKey ; 
            $arr['debitSource'][$temp] = $apTypeRow['sourceamount']; 
            $arr['creditSource'][$temp] = 0; 
            $arr['rate'][$temp] =  $apTypeRow['rate']; 
            $arr['refCashBankKey'][$temp] = '';  
		}

		
		if($multiCurrency){
			// $totalDifference akan terhitugn dua kalo dengan kelebihan bayar
			// meskipun hailnya sama, tp ad perulangan coa
			// jadi potong dulu dengan selisih pembayaran agar gk double
			
			$balance = $rs[0]['balance'] * $rate;
			
			$totalDifference -= $balance;  
			
			if($totalDifference <> 0){ 
             $rsCOA = $coaLink->getCOALink ('lossprofitrate', $warehouse->tableName,$warehousekey, 0); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];     
             $arr['debit'][$temp] = $totalDifference; 
             $arr['credit'][$temp] = 0;
             $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
             $arr['debitSource'][$temp] = $totalDifference; 
             $arr['creditSource'][$temp] =  0; 
             $arr['rate'][$temp] = 1 ;
             $arr['refCashBankKey'][$temp] = '';  
			}
        }
        
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);
	}
	 
	
	
	function getDetailPaymentByAPKey($apkey,$criteria = ''){
		$sql = 'select 
                    '. $this->tableName.'.code,  
                    '. $this->tableName.'.refcode,    
                    '. $this->tableName.'.trdate,  
					'. $this->tableNameDetail.'.* ,
                    '. $this->tableCurrency.'.name as currencyname
				from 
					'. $this->tableNameDetail.','. $this->tableCurrency.','. $this->tableName.'  
				where 
					'. $this->tableNameDetail.'.refkey = '. $this->tableName.'.pkey and
					'. $this->tableNameDetail.'.apkey in(' .$this->oDbCon->paramString($apkey,',').') and
                    '. $this->tableCurrency.'.pkey = '. $this->tableName.'.currencykey and
				    ('. $this->tableName.'.statuskey = 2 or '. $this->tableName.'.statuskey = 3)';
     
        if(!empty($criteria))
            $sql .= $criteria;   
        
        $sql .= ' order by  pkey asc'; 
		
		return $this->oDbCon->doQuery($sql);
	} 
    
    function deleteAPPrepaidTax($id){ 
          
        $apPayableTax23 = $this->getPayableTaxObj();
		
        $rsAPPaymentKey = $this->getTableKeyAndObj($this->tableName, array('key')); 
        $rsAP = $apPayableTax23->searchData('', '', true, ' and refheaderkey = ' . $this->oDbCon->paramString($id) . ' and ' . $apPayableTax23->tableName . '.reftabletype = ' . $rsAPPaymentKey['key'] . ' and ' . $apPayableTax23->tableName . '.statuskey = 1');
    
//        $rsAP = $apPayableTax23->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and '.$apPayableTax23->tableName.'.statuskey = 1');
        //and reftabletype = '.$this->oDbCon->paramString($rsAR[0]['reftabletype']).' 
          
        for($i=0;$i<count($rsAP);$i++) { 
            $arrayToJs = $apPayableTax23->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }  
          
      }
    
    function getAPObj(){
        return new AP();
    }
    
    function getBussinessPartnerObj(){
        return new Supplier();    
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
        $arrParam['selPaymentMethod'] = (!empty($arrParam['selPaymentMethod'])) ? $arrParam['selPaymentMethod'] : array();  
        $arrParam['paymentMethodValue'] = (!empty($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();        
        $arrParam['hidRefKey'] = (isset($arrParam['hidRefKey'])) ? $arrParam['hidRefKey'] : 0; 
        $arrParam['downpaymentAmount'] = (!empty($arrParam['downpaymentAmount'])) ? $arrParam['downpaymentAmount'] : array();
        $arrParam['costAmount'] = (!empty($arrParam['costAmount'])) ? $arrParam['costAmount'] : array();
        $arrParam['trStartDate'] = (!empty($arrParam['trStartDate'])) ? $arrParam['trStartDate'] : DEFAULT_EMPTY_DATE;  
        $arrParam['trEndDate'] = (!empty($arrParam['trEndDate'])) ? $arrParam['trEndDate'] : DEFAULT_EMPTY_DATE;  
        $arrParam['selCurrency'] = (!empty($arrParam['selCurrency'])) ? $arrParam['selCurrency'] : CURRENCY['idr'];
        $arrParam['islinked'] = (!empty($arrParam['islinked'])) ? $arrParam['islinked'] : 0;
        if($arrParam['selCurrency'] == CURRENCY['idr'])  $arrParam['currencyRate']  = 1; 
        $arrParam['taxPeriod'] = (!empty($arrParam['taxPeriod'])) ? date('01 / m / Y',strtotime($_POST['taxPeriod'])) : DEFAULT_EMPTY_DATE;  
        
        $arrJOCodeCache = array();
        $ap = $this->getAPObj();
        
        for($i=0;$i<count($arrParam['hidDetailKey']);$i++){ 
            $arrParam['discount'][$i] = (!empty($arrParam['discount'][$i])) ? $arrParam['discount'][$i] : 0;
            $arrParam['taxPPH'][$i] = (isset($arrParam['taxPPH'][$i])) ? $arrParam['taxPPH'][$i] : 0;   
            $rsAP = $ap->getDataRowById($arrParam['hidAPKey'][$i]); 
            $arrJOCode = explode(',',$rsAP[0]['salesordercodecache']);

            foreach($arrJOCode as $JOCodeRow)
                if(!empty($JOCodeRow))  array_push($arrJOCodeCache, $JOCodeRow);  
        }
           
        $arrJOCodeCache = array_unique($arrJOCodeCache); 

        $arrParam['salesordercodecache'] = implode(', ' ,$arrJOCodeCache);
        // kalo netting 
        $isnetting = (isset($arrParam['nettingkey']) && !empty($arrParam['nettingkey'])) ? true : false;
        
        if( !$isnetting ){  
            foreach($arrParam['paymentMethodValue'] as $key=>$row){ 
                if ($this->unFormatNumber($row) == 0){ 
                    unset($arrParam['selPaymentMethod'][$key]);
                    unset($arrParam['paymentMethodValue'][$key]); 
                    unset($arrParam['hidDetailPaymentKey'][$key]); 
                }
            }
            
            $arrParam['selPaymentMethod'] = array_values($arrParam['selPaymentMethod']);
            $arrParam['selVoucher'] = (isset($arrParam['selVoucher']))  ? array_values($arrParam['selVoucher']) : array();
            $arrParam['paymentMethodValue'] = array_values($arrParam['paymentMethodValue']); 
            $arrParam['hidDetailPaymentKey'] = array_values($arrParam['hidDetailPaymentKey'] ?? []); 
        }else{
            $arrParam['selPaymentMethod'] = array();
            $arrParam['selVoucher'] = array();
            $arrParam['paymentMethodValue'] = array();
            $arrParam['hidDetailPaymentKey'] = array(); 
        }
        
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
       
         if( $isnetting ){  
            $arrParam['selPaymentMethod'] = array('0' => -1);
            $arrParam['paymentMethodValue'] = array('0' => $arrParam['grandtotal']);
            $arrParam['hidDetailPaymentKey'] = array('0' => 0);
            $arrParam['totalPayment'] = $arrParam['grandtotal'];
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
            $apObj = $this->getAPObj();
        
            $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$apObj->tableName.'.code as apcode,
                '.$apObj->tableName.'.trdate as apdate,
                '.$apObj->tableName.'.refcode,
                '.$apObj->tableName.'.refcode2,
                '.$apObj->tableName.'.refinvoicecode,
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
         
	function getDetailPaymentCollections($rs,$indexField,$criteria=''){ 
        $rsAllDetail = $this->getDetailPaymentByAPKey(array_column($rs,'pkey'),$criteria);    
        return $this->reindexDetailCollections($rsAllDetail,$indexField);
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
        if(!is_array($pkey))
            $pkey = array($pkey);
        
        $sql = 'select * from ' . $this->tableFile . ' where refkey in (' . $this->oDbCon->paramString($pkey,',').') order by pkey asc';
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
                        array('tableName' => $this->tableName, 'param' => 'SUPPLIER_NAME', 'tableReference' => array('tableName' => $this->tableSupplier,  
																													 'field' => $this->tableSupplier.'.name',
																													 'refkey' => 'supplierkey'
																													)), 
                        array('tableName' => $this->tableName, 'param' => 'DESCRIPTION', 'field' => $this->tableName.'.trnotes'),    
        );
		
        return $this->stitchDescriptionV2(array('field' => $arrAvailableField, 'pkey' => $arrKey, 'userkey' => $userkey ));
	 }
    
}

?>
