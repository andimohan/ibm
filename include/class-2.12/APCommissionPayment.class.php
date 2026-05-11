<?php

class APCommissionPayment extends APPayment{  
  
    function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ap_commission_payment_header';
		$this->tableNameDetail = 'ap_commission_payment_detail';
		$this->tableSupplier = 'supplier';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tablePayment= 'ap_commission_payment';
		$this->tableCost= 'ap_commission_cost';
	    $this->tableAP = 'ap_commission';
		$this->tableJobCommission = 'emkl_commission_header';   
		$this->tableJobOrder = 'emkl_job_order_header'; 
		$this->tableJobOrderDetailVolume = 'emkl_job_order_detail_volume';
		$this->tableContainer = 'container';
        $this->tableDownpaymentDetail = 'ap_commission_downpayment';// harusnya gk aka npernah kepake
        $this->tableDownpayment = 'supplier_downpayment'; // harusnya gk aka npernah kepake, asal ad dulu. kalo isi ke supplier / customer takut bentrok
        $this->tablePaymentMethod = 'payment_method';
		$this->uploadFileFolder = 'ap-commission-payment/';   
        $this->tableFile = '';

        $this->isTransaction = true; 
        
		$this->securityObject = 'APCommissionPayment';
		
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
		
		 $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
                 
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencyname', 'default'=>true, 'width' => 60,  'align' =>'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'totalpaid','title' => 'payingOffAmount','dbfield' => 'totalpaid', 'default'=>true, 'width' => 120,  'align' =>'right',  'format' => 'number' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'tax23','title' => 'tax23','dbfield' => 'payabletax23', 'default'=>true, 'width' => 100, 'align' =>'right',  'format' => 'number' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'refCode','dbfield' => 'refcode', 'width' => 100));    
 
        //array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
            
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/apCommissionPayment'));
        
        $this->includeClassDependencies(array(
                  'AP.class.php', 
                  'APCommission.class.php', 
                  'APPayment.class.php', 
                  'ChartOfAccount.class.php', 
                  'COALink.class.php', 
                  'Supplier.class.php', 
				  'EMKLCommission.class.php',
				  'EMKLJobOrder.class.php',
				  'Currency.class.php', 
                  'Warehouse.class.php',
                  'PaymentMethod.class.php',
				  'GeneralJournal.class.php',
				  'Downpayment.class.php',
                  'SupplierDownpayment.class.php',
                  'TruckingPurchaseRefund.class.php'
        
        ));  

        $this->overwriteConfig();
	}
	
	function getQuery(){
		
		$sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableSupplier.'.name as suppliername,
			   '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableSupplier.'.accountbank as supplierbankname,
               '.$this->tableSupplier.'.accountname as supplieraccountname,
               '.$this->tableSupplier.'.accountno as supplieraccountno,
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
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
		' .$this->criteria ;
        
                
        $sql .=  $this->getWarehouseCriteria() ;
        
        return $sql;
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
	
	function getDetailInformation($pkey){
		
			$sql = 'select 
				'.$this->tableNameDetail.'.refkey,
				'.$this->tableAP.'.pkey as apkey,
				'.$this->tableAP.'.code as apcode,
				'.$this->tableAP.'.trdate as apdate,
				'.$this->tableAP.'.refcode as aprefcode,
				'.$this->tableAP.'.rate,
				'.$this->tableAP.'.currencykey,
				'.$this->tableAP.'.amount,
				'.$this->tableJobOrder.'.pkey as jokey,
				'.$this->tableJobOrder.'.code as jocode, 
				'.$this->tableJobOrder.'.etdpol, 
				'.$this->tableJobOrder.'.containernumber, 
				'.$this->tableContainer.'.name as itemname,
				'.$this->tableJobOrderDetailVolume.'.qty,
				'.$this->tableJobOrderDetailVolume.'.itemkey,          
               '.$this->tableCurrency.'.name as currencyname
			from 
				'.$this->tableNameDetail.',
				'.$this->tableAP.',
				'.$this->tableJobOrder.',
				'.$this->tableJobOrderDetailVolume.' 
					left join '.$this->tableContainer.' on '.$this->tableJobOrderDetailVolume.'.itemkey = '.$this->tableContainer.'.pkey ,
				'.$this->tableCurrency.'
			where
				'.$this->tableNameDetail.'.refkey in('.$this->oDbCon->paramString($pkey,',').')  and 
				'.$this->tableNameDetail.'.apkey = '.$this->tableAP.'.pkey and 
				'.$this->tableAP.'.refkey2 = '.$this->tableJobOrder.'.pkey  and 
				'.$this->tableAP.'.currencykey = '.$this->tableCurrency.'.pkey  and 
				'.$this->tableJobOrderDetailVolume.'.refkey = '.$this->tableJobOrder.'.pkey  
			';
		
	 	//$this->setLog($sql,true);
	
		$rs = $this->oDbCon->doQuery($sql);
		$rs = $this->reindexDetailCollections($rs,'refkey');
		
		return $rs;
	}
     
	function validateForm($arr,$pkey = ''){
        
		$APObj = $this->getAPObj();
        $downpayment = new SupplierDownpayment();
        
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$supplierkey = $arr['hidSupplierKey'];  
		$arrAPkey = $arr['hidAPKey']; 
		$arrAmount = $arr['amount'];
		$arrOutstanding= $arr['outstanding'];
		$arrDiscount = $arr['discount'];
        $arrDownpaymentKey = $arr['hidDownpaymentKey'];
		$arrDownpaymentAmount = $arr['downpaymentAmount'];
		$arrDownpaymentCode = $arr['downpaymentCode'];
		$refkey = $arr['hidRefKey'];
        $grandtotal = $arr['grandtotal'];
        $trDate = $arr['trDate'];
		$currencykey = $arr['selCurrency']; 
		//$arrPick = $arr['chkPick']; 

        
        $arrDetailKey = array(); 
         
        $rsAP = (!empty($arrAPkey)) ? $APObj->searchData('','',true, ' and '.$APObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrAPkey)).') ') : array(); 
        
        $arrAP = array_column($rsAP, null, 'pkey');
        $arrAPSupplier = array_column($rsAP, 'supplierkey', 'pkey');
        $arrDate = array_column($rsAP, 'trdate', 'pkey');
        
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
         
        if ($grandtotal < 0)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['apPayment'][3]); 	
            
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
                $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]]. '</strong>. ' . $this->errorMsg['apPayment'][2]); 
               
                if ($arrAPSupplier[$arrAPkey[$i]] <> $supplierkey) 
                    $this->addErrorList($arrayToJs,false,$arrAP[$arrAPkey[$i]]['code']. '. ' . $this->errorMsg['ap'][5]); 
				
                if($arr['selWarehouseKey']<>$arrAP[$arrAPkey[$i]]['warehousekey'])
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]]['code'].'</strong>. '.$this->errorMsg[905]); 

				 if($currencykey<>$arrAP[$arrAPkey[$i]]['currencykey'])
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]]['code'].'</strong>. '.$this->errorMsg['arPayment'][5]); 
                 
                
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
    
	  
	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $supplierkey =  $rsHeader[0]['supplierkey'];
		$currencykey =  $rsHeader[0]['currencykey'];
        $employeekey =  $rsHeader[0]['supplierkey'];
        
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
							$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '. $this->errorMsg['coa'][3]);
					}
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
       
        $rsAP = $ap->searchData('','',true,' and ' .$ap->tableName.'.pkey in ('.$arrKeys.') ' );
        $trDate =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        for($i=0;$i<count($rsAP);$i++){
            $apDate = $this->formatDBDate($rsAP[$i]['trdate'],'d / m / Y');
            $dateDiff = $this->dateDiff($trDate,$apDate);
            if($dateDiff > 0)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['apPayment'][4]); 
        }
 	 }
    
	
	// coba inherit dr parent
//	function confirmTrans($rsHeader){
//		//update jurnal umum 
//		$rsPayment = $this->getPaymentMethodDetail($rsHeader[0]['pkey']); 
//        $this->updateGL($rsHeader,$rsPayment);
//	}
    
//    function validateCancel($rsHeader, $autoChangeStatus = false){  
//        $id = $rsHeader[0]['pkey']; 
//        $ap = $this->getAPObj(); 
//	 } 
//	 
 
//	function cancelTrans($rsHeader,$copy){ 
//
//        $id = $rsHeader[0]['pkey'];
//		
//		if ($copy)
//			$this->copyDataOnCancel($id);	  
//		  
//        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
//	}
	
	 
	function updateGL($rs,$rsPayment){
        if (!USE_GL) return;
        if ($rs[0]['overwriteGL'] == 1) return;
        
		$warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal();
        $supplier = new Supplier();
        $costCashOut = new CostCashOut();
		$cashBank = new CashBank();
		$chartOfAccount = new ChartOfAccount();
		
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
        array_push($desc,$rsSupplier[0]['name']); 
        if(!empty($rs[0]['trnotes'])) array_push($desc,$rs[0]['trnotes']);
		$arr['trDesc'] = implode(chr(13),$desc);  
		
		$temp = -1; 
		
		
		if(ADV_FINANCE && TEST_VOUCHER) 
			$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey'],'',2);
		
        for($i=0;$i<count($rsPayment); $i++){ 
			
			 
            if(ADV_FINANCE && TEST_VOUCHER){  
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
             $arr['debit'][$temp] = 0; 
             $arr['credit'][$temp] = $paymentAmount; 
             $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  
			 $totalPaymentAmount += $paymentAmount;
        }
        		
        $rsDownpayment = $this->getDownpaymentDetail($rs[0]['pkey'],'',false);  
        for($i=0;$i<count($rsDownpayment); $i++){  
			 $downpaymentAmount = $rsDownpayment[$i]['amount'] * $rsDownpayment[$i]['downpaymentrate'];
             $temp++;
             $arr['hidCOAKey'][$temp] = $supplier->getDownpaymentCOAKey($rs[0]['supplierkey'],$warehousekey);   
             $arr['debit'][$temp] = 0; 
             $arr['credit'][$temp] = $downpaymentAmount; 
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
             $arr['refCashBankKey'][$temp] = '';  
            
             $totalPaymentAmount -= $costAmount; // kalo ditambah malah jadi muncul jurnal adjustment, jadi harus dikurang, TEL APCP2409000087 case nya.
        }
        
         $tax23Amount = $rs[0]['payabletax23'] * $rate;
         $rsCOA = $coaLink->getCOALink ('payabletax23', $warehouse->tableName,$warehousekey,0); 
         $temp++;
         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
         $arr['debit'][$temp] = 0; 
         $arr['credit'][$temp] = $tax23Amount;  
         $arr['refCashBankKey'][$temp] = '';  
		 $totalPaymentAmount += $tax23Amount;
 
        $temp++; 
        $rsCOA = $coaLink->getCOALink ('purchaseretaildiscount', $warehouse->tableName,$warehousekey, 0); // ini harus dipisah antara jasa / retail sepertinya
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['totaldiscount']  * $rate;  
        $arr['refCashBankKey'][$temp] = '';  
		 
        //selisih pembayaran   
        $temp++; 
        if ($rs[0]['balance'] < 0){ 
            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = abs($rs[0]['balance'])  * $rate; 
            $arr['refCashBankKey'][$temp] = '';  
        }else{ 
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = abs($rs[0]['balance'])  * $rate; 
            $arr['credit'][$temp] = 0; 
            $arr['refCashBankKey'][$temp] = '';  
        }

        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
		
		$ap = $this->getAPObj();
		$rsDetail = $this->getDetailById($rs[0]['pkey']);
		$totalAP = 0;
        
        $rsAPCol = $ap->searchDataRow(array($ap->tableName.'.pkey', $ap->tableName.'.amountidr', $ap->tableName.'.amount', $ap->tableName.'.rate'),
                                      ' and '.$ap->tableName.'.pkey in ('.$this->oDbCon->paramString(array_column($rsDetail,'apkey'),',').')'
                                     );
        $rsAPCol = array_column($rsAPCol,null,'pkey');
		foreach($rsDetail as $key=>$rowDetail){ 
            $rsAp = $rsAPCol[$rowDetail['apkey']]; 
            $totalAP += ($rowDetail['amount'] * $rsAp['rate']);
        }
		
        
		$totalDifference = $totalPaymentAmount - $totalAP - ($rs[0]['balance']*$rate); // harus potong balance, TEL APCP2409000087 case nya
         
        $temp++; 
		$arr['hidCOAKey'][$temp] =  $supplier->getCommissionCOAKey($rs[0]['supplierkey'],$warehousekey);
		$arr['debit'][$temp] = $totalAP + $totalDiscount;
		$arr['credit'][$temp] = 0;
        $arr['refCashBankKey'][$temp] = '';  
		
		if($multiCurrency && $totalDifference <> 0){
             $rsCOA = $coaLink->getCOALink ('lossprofitrate', $warehouse->tableName,$warehousekey, 0); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];     
             $arr['debit'][$temp] = $totalDifference; 
             $arr['credit'][$temp] = 0;
        }
   
//        $this->setLog($arr,true);
        
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);
	}
    
    
    function getAPObj(){
        return new APCommission();
    }
    
    function getBussinessPartnerObj(){
        return new Supplier();    
    } 
       
    function afterAddDataOnCopy($pkey, $oldkey){  
      
    }
//
//
//
//    function afterStatusChanged($rsHeader){ 
//        
//        // ad update DP
//		parent::afterStatusChanged($rsHeader);
//        
//        if ($this->loadSetting('lockJobAfterCommissionPaid') == 1){
//            // update satus job sdh ad pembayaran komisi blm
//            $APObj = $this->getAPObj(); 
//            $id = $rsHeader[0]['pkey'];
//            $rsHeader = $this->getDataRowById($id);
//            $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
//
//            for($i=0;$i<count($rsDetail); $i++){    
//                //update Job Order iscommissionpaid
//                $APObj->updateJobOrderCommissionIsPaid($rsDetail[$i]['apkey'], $rsHeader[0]['statuskey']);
//            }
//        } 
//       
//    }
// 
}

?>
