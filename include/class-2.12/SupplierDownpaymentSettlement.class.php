<?php

class SupplierDownpaymentSettlement extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'supplier_downpayment_settlement_header';
		$this->tableNameDetail = 'supplier_downpayment_settlement_detail';
		$this->tableSupplier = 'supplier';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tablePayment= 'supplier_downpayment_settlement_payment';
        $this->tableCost = 'supplier_downpayment_settlement_cost';
        $this->tableCurrency = 'currency';
        $this->tableItem = 'cost_cash_out';
        $this->tablePaymentMethod = 'payment_method';
        $this->tableCashBank = 'cash_bank';
        $this->isTransaction = true;
    		 
		$this->securityObject = 'SupplierDownpaymentSettlement';
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['downpaymentkey'] = array('hidDownpaymentKey');
        $this->arrDataDetail['outstanding'] = array('outstanding','number');
        $this->arrDataDetail['amount'] = array('amount', array('datatype' => 'number','mandatory'=>true));
        $this->arrDataDetail['discount'] = array('discount','number');
        $this->arrDataDetail['taxamount'] = array('taxPPH','number');
       
        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
        $arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');  // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja

       
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
        $this->arrData['refcode'] = array('refHeaderCode');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['currencykey'] = array('selCurrency');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trnotes'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['grandtotal'] = array('grandtotal','number');
        $this->arrData['totalpayment'] = array('totalPayment','number');
        $this->arrData['totaldownpayment'] = array('totalDownpayment','number');
        $this->arrData['totalcost'] = array('totalCost','number');
        $this->arrData['totalcoa'] = array('subtotalCOA','number');
        $this->arrData['typekey'] = array('selDPSettlementType');
        $this->arrData['coakey'] = array('hidCOAKey');
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
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname', 'default'=>true, 'width' => 60,  'align' =>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'payingOffAmount','title' => 'payingOffAmount','dbfield' => 'totalreceived', 'default'=>true, 'width' => 120,  'align' =>'right',  'format' => 'number' ));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'tax23','title' => 'tax23','dbfield' => 'prepaidtax23', 'default'=>true, 'width' => 100, 'align' =>'right',  'format' => 'number' ));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'grandtotal', 'default'=>true, 'width' => 100, 'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'refCode','dbfield' => 'refcode', 'width' => 100));    
 
       
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/supplierDownpaymentSettlement'));
          
        $this->includeClassDependencies(array(
                  'CashBank.class.php', 
                  'ChartOfAccount.class.php', 
                  'COALink.class.php', 
                  'Currency.class.php', 
                  'Supplier.class.php',  
                  'Downpayment.class.php',
                  'SupplierDownpayment.class.php',  
                  'GeneralJournal.class.php', 
                  'CostCashOut.class.php', 
                  'Service.class.php', 
                  'Warehouse.class.php',
            ));  
       
        $this->overwriteConfig();
	}
	
	function getQuery(){
		
		$sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableSupplier.'.name as suppliername,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname,
               '.$this->tableCurrency.'.name as currencyname
			FROM '.$this->tableStatus.',
                 '.$this->tableSupplier.', 
                  '.$this->tableName.', 
                  '.$this->tableWarehouse.',
                  '.$this->tableCurrency.'
			WHERE '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey and
				  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.currencykey = '.$this->tableCurrency.'.pkey   and
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
		' .$this->criteria ;
        
        $sql .=  $this->getWarehouseCriteria() ;
        
        return $sql;
	}
    

	
	function reCountGrandtotal($arrParam){

				$grandtotal = 0;
				$amount = 0;
				$discount = 0;
				$pph = 0;
				
				$arrDPkey = $arrParam['hidDownpaymentKey'];
				$arrAmount = $arrParam['amount'];
				$arrDiscount = $arrParam['discount'];
				$arrPph = $arrParam['taxPPH'];
        
                $typeKey = $arrParam['selDPSettlementType'];
                $totalCOA = $this->unFormatNumber($arrParam['totalCOA']);
				//$arrPick = $arrParam['chkPick']; 
				
				$arrDownpaymentDetail = array(); 
				
				for ($i=0;$i<count($arrDPkey);$i++){
					
				    $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
					if ( empty($arrDPkey[$i]) || empty($arrAmount[$i]))  //  || empty($arrPick[$i]) 
						continue; 
					
						$amount += $this->unFormatNumber($arrAmount[$i]);

                     
				}  
        
        
                // total yg dilunasin
                $totalreceived = $amount ;

                 
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
         
				$grandtotal = $amount - $totalCost; 
				
				$balance = 0;
				$totalPayment = 0; 
				$payment = $arrParam['paymentMethodValue'];
				for($i=0;$i<count($payment);$i++){
					$totalPayment += $this->unFormatNumber($payment[$i]);
				} 
          
				$balance = ($typeKey == 1) ? $totalPayment - $grandtotal : $totalCOA - $grandtotal;

				$reCountResult = array();
				$reCountResult['totalReceived'] = $totalreceived;
				$reCountResult['totalDiscount'] = $discount;
//				$reCountResult['pph23'] = $pph;
				$reCountResult['totalPayment'] = $totalPayment;
				$reCountResult['totalCOA'] = $totalCOA;
				$reCountResult['grandtotal'] = $grandtotal;
				$reCountResult['balance'] = $balance;
				$reCountResult['totalCost'] = $totalCost;
				
				return $reCountResult;
				
	}
	 
	function validateForm($arr,$pkey = ''){
          
        $DPObj = $this->getDownpaymentObj();
            
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$supplierkey = $arr['hidSupplierKey'];  
		$currencykey = $arr['selCurrency'];  
		$arrDownpaymentKey = $arr['hidDownpaymentKey']; 
		$arrAmount = $arr['amount'];
		$arrOutstanding= $arr['outstanding'];
        $trDate = $arr['trDate'];
		//$arrPick = $arr['chkPick'];  

        $arrDetailKey = array();
          
       // $this->setLog($arrARkey,true);
         
        $rsDP = (!empty($arrDownpaymentKey)) ? $DPObj->searchData('','',true, ' and '.$DPObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrDownpaymentKey)).') ') : array(); 
        
        $arrDP = array_column($rsDP,null, 'pkey');
        $arrDPSupplier = array_column($arrDP, 'supplierkey', 'pkey');
        $arrDate = array_column($rsDP, 'trdate', 'pkey');
            
         
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
			
		if(empty($supplierkey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['supplier'][1]);
	 
        $hasDP = false; 
        for($i=0;$i<count($arrDownpaymentKey);$i++) { 
            if (!empty($arrDownpaymentKey[$i]))  //  && !empty($arrPick[$i])
                $hasDP = true;  

            if (in_array($arrDownpaymentKey[$i],$arrDetailKey)){   
                $this->addErrorList($arrayToJs,false, $arrDP[$arrDownpaymentKey[$i]]['code'].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKey, $arrDownpaymentKey[$i]); 
            }

        }

        if (!$hasDP)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['downpayment'][1]); 	

        
		for($i=0;$i<count($arrDownpaymentKey);$i++) {  
            if(!empty($arrDownpaymentKey[$i])){
                
                $outstanding = $this->unFormatNumber($arrOutstanding[$i]);
                $amount = $this->unFormatNumber($arrAmount[$i]);

                if ($amount == 0 || 
                    ($outstanding > 0 && $amount < 0) || 
                    ($outstanding > 0 && $amount > $outstanding) || //overpay
                    ($outstanding < 0 && ($amount < $outstanding ||  $amount > 0)) //overpay
                   ) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$arrDP[$arrDownpaymentKey[$i]]['code']. '</strong>. ' . $this->errorMsg['downpaymentSettlement'][2]);
               

                if ($arrDPSupplier[$arrDownpaymentKey[$i]] <> $supplierkey)
                    $this->addErrorList($arrayToJs,false,$arrDP[$arrDownpaymentKey[$i]]['code']. '. ' . $this->errorMsg['supplierDownpayment'][5]); 

				if($currencykey<>$arrDP[$arrDownpaymentKey[$i]]['currencykey'])
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrDP[$arrDownpaymentKey[$i]]['code'].'</strong>. '.$this->errorMsg['downpaymentSettlement'][5]); 
  
                $dpDate = $this->formatDBDate($arrDate[$arrDownpaymentKey[$i]],'d / m / Y');
                $dateDiff = $this->dateDiff($trDate,$dpDate); 
                if($dateDiff > 0)
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrDP[$arrDownpaymentKey[$i]]['code'].'</strong>.'. $this->errorMsg['downpaymentSettlement'][4]);
                  
            }
		}
         
  
         
		
		return $arrayToJs;
	 }
	     
    function afterStatusChanged($rsHeader){ 
        
        $DPobj = $this->getDownpaymentObj();
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsDetail); $i++){  
           $DPobj->updateOutstanding($rsDetail[$i]['downpaymentkey']); 
        }

         // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3,'',false,true); // set jd autochange, agar gk perlu validasi session utk AR Payment yg jalan dr cron 
    }
    
    function afterUpdateData($arrParam, $action){  
        $pkey = $arrParam['pkey'];  
	}
 

	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $supplierkey =  $rsHeader[0]['supplierkey'];
        $currencykey =  $rsHeader[0]['currencykey'];
        $rate = ($rsHeader[0]['rate'] > 0) ? $rsHeader[0]['rate'] : 1;
                
		$coaLink = new COALink();
        $warehouse = new Warehouse();
        $supplierDownpayment = $this->getDownpaymentObj();
          
        if($rsHeader[0]['typekey'] == 1){
            $rsPayment = (ADV_FINANCE && TEST_VOUCHER) ?  $this->getPaymentVoucherDetail($id,'',2) : $this->getPaymentMethodDetail($id);  
      
            $totalPayment = 0;
            for($i=0;$i<count($rsPayment); $i++)
                $totalPayment += $rsPayment[$i]['amount'];
        }else{
            $totalPayment =  $rsHeader[0]['totalcoa'];

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
            array_push($arrCOA, 'supplierdownpayment' , 'otherrevenue','othercost'); 
            
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
            


                if($rsHeader[0]['typekey'] == 1){
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
                }else{
                    if(empty($rsHeader[0]['coakey']))
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][1]); 

                }
        }    
 
        $rsDetail = $this->getDetailById($id);
        $arrKeys = array_column($rsDetail,'downpaymentkey');  
        $rsDownpayment = $supplierDownpayment->searchData('','',true,' and ' .$supplierDownpayment->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') ' );
      
        
        // cek status dp sudah lunas atau blm
        if (!empty($arrKeys)){ 
            $rsDPPaid = $supplierDownpayment->searchData('','',true,' and ' .$supplierDownpayment->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') and ' .$supplierDownpayment->tableName.'.statuskey in (3,4) ' );
            if (!empty($rsDPPaid)){
                $arrDP = array_column($rsDPPaid,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br><strong>'.implode(', ',$arrDP).'</strong>. '.$this->errorMsg['downpaymentSettlement'][6]); 
            }
        }
        
 
        
        $trDate =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        for($i=0;$i<count($rsDownpayment);$i++){
            /*
            // pelunasan dr customer tdk bisa dikontrol mau byr ke cabang mana
            if($rsHeader[0]['warehousekey']<>$rsAR[$i]['warehousekey'])
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$rsAR[$i]['code'].'</strong>. '.$this->errorMsg[905]); */
 
			
			if($currencykey<>$rsDownpayment[$i]['currencykey'])
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. <strong>'.$rsDownpayment[$i]['code'].'</strong>. '.$this->errorMsg['downpaymentSettlement'][5]); 
                 $arDate = $this->formatDBDate($rsDownpayment[$i]['trdate'],'d / m / Y');
                $dateDiff = $this->dateDiff($trDate,$arDate);
                if($dateDiff > 0)
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['downpaymentSettlement'][4]); 
           
        }
        
        
        // cek setiap detail ad g overpaid gk
        // harus ambil ulang outstanding AR
        $rsDownpayment = array_column($rsDownpayment,null,'pkey');
        foreach($rsDetail as $detailRow){
           $dpRow = $rsDownpayment[$detailRow['downpaymentkey']];
            
           //$this->setLog($detailRow['amount'].'+'.$detailRow['discount'] .'>'. $arRow['outstanding'],true);
            
           if ( ($dpRow['outstanding'] > 0 && ($detailRow['amount']> ($dpRow['outstanding']+1) ) || //overpay
               ($dpRow['outstanding']  < 0 && ($detailRow['amount']< ($dpRow['outstanding']-1)  ||  $detailRow['amount'] > 0)) //overpay
              ))
            $this->addErrorLog(false,'<strong>'.$dpRow['code']. '</strong>. ' . $this->errorMsg['downpaymentSettlement'][2]); 
           
        }
         
        
    }
	 
	function confirmTrans($rsHeader){
		$id = $rsHeader[0]['pkey']; 

		$arrCashBank = array();
		if( $this->isActiveModule('CashBank') ){
       		 $coaLink = new COALink();  
			 $cashBank = new CashBank(); 
			 $warehouse = new Warehouse();
            
            if (ADV_FINANCE && TEST_VOUCHER){ 
                $rsPayment = $this->getPaymentVoucherDetail($id,'',2);
                $rsDPKey = $this->getTableKeyAndObj($this->tableName,array('key')); 
                // update outstanding voucher  
                foreach($rsPayment as $voucherlist){ 
                    $cashBank->insertTransaction(
                        array('refkey' => $voucherlist['cashbankvoucherkey'],
                              'reftablekey' => $rsDPKey['key'],
                              'reftranskey' => $rsHeader[0]['pkey'],
                              'refcode' => $rsHeader[0]['code'],
                              'refdate' => $rsHeader[0]['trdate'],
                              'amount' => $voucherlist['amount'],
                    ),true
                    ); 
                }
            }else{   
			     $rsPayment = $this->getPaymentMethodDetail($id);

                // kalo ad pembayaran   
                for($i=0;$i<count($rsPayment); $i++){   
                    if (USE_GL) {
                       $rsPaymentCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                       $coakey = $rsPaymentCOA[0]['coakey']; 
                   }else{
                       $coakey = $rsPayment[$i]['paymentkey'];
                   }

                   $rsCashBank = $cashBank->addCashBank($rsHeader,$this->tableName, array('supplierkey' => $rsHeader[0]['supplierkey'],'coakey' => $coakey, 'amount' => $rsPayment[$i]['amount'])); 
                   $rsPayment[$i]['cashBankKey'] = $rsCashBank['pkey'];
                }       
            }

		}
		
		
		//update jurnal umum 
        $this->updateGL($rsHeader,$rsPayment); 
	}
    
    function validateCancel($rsHeader,$autoChangeStatus = false){ 
            

            if ( !$autoChangeStatus ) {
                if(isset($rsHeader[0]['islinked']) && !empty($rsHeader[0]['islinked']))
                    $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'].'.</strong> '.$this->errorMsg[900],true);  
            }  

            $id = $rsHeader[0]['pkey'];
        
            if(!$this->validateAutoReverseGL($id))
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['generalJournal'][6],true);


    } 

    function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey'];   
                $rsDPKey = $this->getTableKeyAndObj($this->tableName,array('key')); 

		if( $this->isActiveModule('CashBank') ){
			$cashBank = new CashBank();
			if (ADV_FINANCE && TEST_VOUCHER){ 
				$cashBank->removeTransaction($id,$rsDPKey['key']);
			}else{ 
				$cashBank->cancelCashBank($rsHeader,$this->tableName);
			}
		}
		
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
        $chartOfAccount = new ChartOfAccount();
        $costCashOut= new CostCashOut();
        $multiCurrency = ($rs[0]['currencykey'] != CURRENCY['idr']) ? true : false; // khusus currency selain IDR
        $totalPayment = 0;
        
        $warehousekey = $rs[0]['warehousekey'];
        $typekey = $rs[0]['typekey'];
        $rate = (!empty($rs[0]['rate'])) ? $rs[0]['rate'] : 1;
        $currencykey = $rs[0]['currencykey'];
                    
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
        $rsSupplier = $supplier->getDataRowById($rs[0]['supplierkey']);
        array_push($desc,$rsSupplier[0]['name']); 
        if(!empty($rs[0]['trnotes'])) array_push($desc,$rs[0]['trnotes']);
		$arr['trDesc'] = implode(chr(13),$desc);
		
		$temp = -1; 
             
        $totalPaymentAmount = 0; 
        
        if(ADV_FINANCE && TEST_VOUCHER) 
			$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);
        
        // kalo pake voucher, potong counter coa
        if($typekey == 1){
            for($i=0;$i<count($rsPayment); $i++){ 
      
                if(ADV_FINANCE && TEST_VOUCHER){ 
    //				$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2); // harusnya udah gk perlu
                    $rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
                    $rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);

                    $paymentcoakey = $rsCOA[0]['countercoakey'];
                 }else{
                    $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
                    $paymentcoakey = $rsCOA[0]['coakey'];
                 }
                
                 $paymentAmount = $rsPayment[$i]['amount'] * $rate;
                 $temp++;
                 $arr['hidCOAKey'][$temp] = $paymentcoakey;
                 $arr['debit'][$temp] = $paymentAmount; 
                 $arr['credit'][$temp] = 0;  
                 $arr['selCurrencyKey'][$temp] = $currencykey ; 
                 $arr['debitSource'][$temp] = $rsPayment[$i]['amount']; 
                 $arr['creditSource'][$temp] = 0; 
                 $arr['rate'][$temp] = $rate ;
             	 $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  

                 $totalPaymentAmount += $paymentAmount; 

            }
        }else{
                $paymentAmount = $rs[0]['totalcoa'] * $rate;
                 $temp++;
                 $arr['hidCOAKey'][$temp] = $rs[0]['coakey'];
                 $arr['debit'][$temp] = $paymentAmount; 
                 $arr['credit'][$temp] = 0;   
                 $arr['selCurrencyKey'][$temp] = CURRENCY['idr']; 
                 $arr['debitSource'][$temp] = $paymentAmount; 
                 $arr['creditSource'][$temp] = 0; 
                 $arr['rate'][$temp] = 1 ;
             	 $arr['refCashBankKey'][$temp] = '';  

                 $totalPaymentAmount += $paymentAmount; 

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
             $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
             $arr['debitSource'][$temp] = $costAmount; 
             $arr['creditSource'][$temp] = 0; 
             $arr['rate'][$temp] = 1 ;
             $arr['refCashBankKey'][$temp] = '';  

             $totalPaymentAmount += $costAmount; 

        }


        //selisih pembayaran     
		$balance = abs($rs[0]['balance']) * $rate; 
		$temp++; 
		if ($rs[0]['balance'] < 0){  
			$rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
			$arr['debit'][$temp] = $balance; 
			$arr['credit'][$temp] = 0;         
            $arr['selCurrencyKey'][$temp] =  CURRENCY['idr'] ; 
            $arr['debitSource'][$temp] = $balance; 
            $arr['creditSource'][$temp] = 0; 
            $arr['rate'][$temp] = 1 ;

			// akan aneh kalo ad selisih pembayran, harus disesuaikan dengan selisih pembayaran
			$totalPaymentAmount += $balance; 
		}else { 
			$rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
			$arr['debit'][$temp] = 0; 
			$arr['credit'][$temp] = $balance; 
            $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] = $balance; 
            $arr['rate'][$temp] = 1 ;

			// akan aneh kalo ad selisih pembayran, harus disesuaikan dengan selisih pembayaran
			$totalPaymentAmount -= $balance;  
		}
		$arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		$arr['refCashBankKey'][$temp] = '';  
	 
       
        
        $supplierDownpayment = $this->getDownpaymentObj();
        $rsDetail = $this->getDetailById($rs[0]['pkey']);
        
        $totalDP = 0;
        
        $rsDPCol = $supplierDownpayment->searchDataRow(array($supplierDownpayment->tableName.'.pkey', $supplierDownpayment->tableName.'.amount', $supplierDownpayment->tableName.'.rate',$supplierDownpayment->tableName.'.currencykey', $supplierDownpayment->tableName.'.reftabletype'),
                                      ' and '.$supplierDownpayment->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'downpaymentkey'),',').')'
                                     );
        $rsDPCol = array_column($rsDPCol,null,'pkey');
        
            
        // kalo ad laba rugi langsung dihitung saja selisihnya,

        $supplierDPCOAKey = $supplier->getDownpaymentCOAKey($rs[0]['supplierkey'],$warehousekey);
        foreach($rsDetail as $key=>$rowDetail){ 
             
            $rsDP = $rsDPCol[$rowDetail['downpaymentkey']];  
            $totalDP += ($rowDetail['amount'] * $rsDP['rate']);
            //$totalDifference += (($rate - $rsAr['rate']) * $rowDetail['amount']);
            

            $temp++; 
            $arr['hidCOAKey'][$temp] =  $supplierDPCOAKey;
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $rowDetail['amount'] * $rsDP['rate']; // dulu ini totalreceived
            $arr['selCurrencyKey'][$temp] =  $rsDP['currencykey'] ; 
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] = $rowDetail['amount'] ; 
            $arr['rate'][$temp] = $rsDP['rate'] ;
            $arr['refCashBankKey'][$temp] = '';  

        }

      
        // akan aneh kalo ad selisih pembayran, harus disesuaikan dengan selisih pembayaran
        $totalPaymentAmount -= $balance; 
          
        // jika $totalDifference > 0 berarti laba kurs
        $totalDifference = $totalPaymentAmount - $totalDP;

//       dipindah keatas karena kalo multi currecny harus per baris ratenya
//        $temp++; 
//        $arr['hidCOAKey'][$temp] =  $supplier->getDownpaymentCOAKey($rs[0]['supplierkey'],$warehousekey);
//        $arr['debit'][$temp] = 0; 
//        $arr['credit'][$temp] = $totalDP ; // dulu ini totalreceived
//		$arr['refCashBankKey'][$temp] = '';  
 
        if($multiCurrency && $totalDifference <> 0){
             $rsCOA = $coaLink->getCOALink ('lossprofitrate', $warehouse->tableName,$warehousekey, 0); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];     
             $arr['debit'][$temp] = 0; 
             $arr['credit'][$temp] = $totalDifference; // plus minus nanti otomatis dibalik, jgn di if else, malah jd error 
             $arr['selCurrencyKey'][$temp] =  CURRENCY['idr']; 
             $arr['debitSource'][$temp] = 0; 
             $arr['creditSource'][$temp] = $totalDifference;
             $arr['rate'][$temp] = 1; 
			 $arr['refCashBankKey'][$temp] = '';  

        }
		$arrayToJs = $generalJournal->addData($arr);
        
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 

    }
      
	function getDetailPaymentByDownpaymentKey($downpaymentkey,$criteria = ''){
		$sql = 'select 
                    '. $this->tableName.'.code,  
                    '. $this->tableName.'.refcode,  
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
					'. $this->tableNameDetail.'.downpaymentkey in (' .$this->oDbCon->paramString($downpaymentkey,',').') and
				    ('. $this->tableName.'.statuskey = 2 or '. $this->tableName.'.statuskey = 3) ';
        
        if(!empty($criteria))
            $sql .= $criteria;   
        
        $sql .= ' order by  pkey asc'; 
					  
		return $this->oDbCon->doQuery($sql);
	}  



    function getDownpaymentObj(){
        return new SupplierDownpayment();
    }
    
    function normalizeParameter($arrParam, $trim = false){   
        $arrParam = parent::normalizeParameter($arrParam);
         
        $arrParam['pph23'] = (!empty($arrParam['pph23'])) ? $arrParam['pph23'] : 0;
        $arrParam['balance'] = (!empty($arrParam['balance'])) ? $arrParam['balance'] : 0;
        $arrParam['totalPayment'] = (!empty($arrParam['totalPayment'])) ? $arrParam['totalPayment'] : 0;
        $arrParam['totalDiscount'] = (!empty($arrParam['totalDiscount'])) ? $arrParam['totalDiscount'] : 0;
        $arrParam['selPaymentMethod'] = (!empty($arrParam['selPaymentMethod'])) ? $arrParam['selPaymentMethod'] : 0;
        $arrParam['paymentMethodValue'] = (!empty($arrParam['paymentMethodValue'])) ? $arrParam['paymentMethodValue'] : array();
  	    $arrParam['costAmount'] = (!empty($arrParam['costAmount'])) ? $arrParam['costAmount'] : array();
  	    $arrParam['hidCostKey'] = (!empty($arrParam['hidCostKey'])) ? $arrParam['hidCostKey'] : array();
        $arrParam['trStartDate'] = (!empty($arrParam['trStartDate'])) ? $arrParam['trStartDate'] : DEFAULT_EMPTY_DATE;  
        $arrParam['trEndDate'] = (!empty($arrParam['trEndDate'])) ? $arrParam['trEndDate'] : DEFAULT_EMPTY_DATE;
        $arrParam['selCurrency'] = (!empty($arrParam['selCurrency'])) ? $arrParam['selCurrency'] : CURRENCY['idr'];
        $arrParam['selDPSettlementType'] = (!empty($arrParam['selDPSettlementType'])) ? $arrParam['selDPSettlementType'] : 0;
        $arrParam['islinked'] = (!empty($arrParam['islinked'])) ? $arrParam['islinked'] : 0;

        if($arrParam['selDPSettlementType'] == 2){
                    
            $arrParam['hidCOAKey'] = (!empty($arrParam['hidCOAKey'])) ? $arrParam['hidCOAKey'] : 0;
            $arrParam['selPaymentMethod'] = array();
            $arrParam['selVoucher'] = array();
            $arrParam['paymentMethodValue'] = array();
            $arrParam['hidDetailPaymentKey'] = array(); 

            
            
        }else{
            
            $arrParam['totalCOA'] = 0;
            $arrParam['hidCOAKey'] = 0;
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
        }

        
        // remove uncheck 
        $this->removeUnCheckRows($arrParam,$this->arrDataDetail);
        
        $reCountResult = $this->reCountGrandtotal($arrParam);
         
        $arrParam['totalReceived'] = $reCountResult['totalReceived'];
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['totalCost'] = $reCountResult['totalCost'];
        $arrParam['totalCOA'] = $reCountResult['totalCOA'];
        $arrParam['balance'] = $reCountResult['balance']; 
        
        
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
           
      $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$this->tableName.'.code as dpcode , 
                '.$this->tableName.'.trdate
			  from
			  	'. $this->tableNameDetail .',
                '.$this->tableName.' 
			  where
			  	'. $this->tableNameDetail .'.downpaymentkey = '.$this->tableName.'.pkey and
			  	'. $this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').') ';
         
       
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   } 
     
    function getDetailPaymentCollections($rs,$indexField,$criteria=''){ 
        $rsAllDetail = $this->getDetailPaymentByDownpaymentKey(array_column($rs,'pkey'),$criteria);    
        return $this->reindexDetailCollections($rsAllDetail,$indexField);
    }

    
    

    
	
}
?>
