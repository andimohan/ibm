<?php

class AREmployeePayment extends ARPayment{
	
    function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ar_employee_payment_header';
		$this->tableNameDetail = 'ar_employee_payment_detail';
		$this->tableEmployee = 'employee'; 
		$this->tablePayment = 'ar_employee_payment';
        $this->tableDownpaymentDetail = 'ar_employee_downpayment'; // harusnya gk aka npernah kepake
        $this->tableDownpayment = 'employee_downpayment'; // harusnya gk aka npernah kepake
		$this->tableAR = 'ar_employee';   
         
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['arkey'] = array('hidARKey');
        $this->arrDataDetail['outstanding'] = array('outstanding','number');
        $this->arrDataDetail['amount'] = array('amount', array('datatype' => 'number','mandatory'=>true));
        $this->arrDataDetail['discount'] = array('discount','number');
        $this->arrDataDetail['taxamount'] = array('taxPPH','number');
        
        $this->arrPaymentDetail = array(); 
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); 
        $this->arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');  // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
       
    
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));
   
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['nettingkey'] = array('nettingkey');
        $this->arrData['code'] = array('code');
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
        $this->arrData['prepaidtax23'] = array('pph23','number'); 
        $this->arrData['totalreceived'] = array('totalReceived','number');
        $this->arrData['donumbercache'] = array('doNumber');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['usedateperiod'] = array('chkDatePeriod');
        $this->arrData['startdateperiod'] = array('trStartDate','date');
        $this->arrData['enddateperiod'] = array('trEndDate','date');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['refapemployeecommissionkey'] = array('refAPEmployeeCommissionKey');
        $this->arrData['refapemployeecommissiondetailkey'] = array('refAPEmployeeCommissionDetailKey');
        
		$this->securityObject = 'AREmployeePayment';
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'grandtotal', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
      
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/arEmployeePayment'));
        
        $this->includeClassDependencies(array(
                  'ARPayment.class.php',
                  'AREmployee.class.php'  
        ));  
       
        
        $this->overwriteConfig();
        
        
	}
    
     function getQuery(){
		
		return'
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableEmployee.'.name as employeename,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.', '.$this->tableEmployee.', '.$this->tableName.', '.$this->tableWarehouse.'
			WHERE '.$this->tableName.'.customerkey = '.$this->tableEmployee.'.pkey and
				  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
		' .$this->criteria ;
	}
    
	  
    function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey']; 
           	
        $ARObj = $this->getARObj();  
        $rsDetail = $this->getDetailById($id);
        $arrKeys = array_column($rsDetail,'arkey');
        $customerkey =  $rsHeader[0]['customerkey'];

//        $rsPayment = $this->getPaymentMethodDetail($id);
        $rsPayment = (ADV_FINANCE && TEST_VOUCHER) ?  $this->getPaymentVoucherDetail($id,'',3) : $this->getPaymentMethodDetail($id); 
     
        $isnetting = (isset($rsHeader[0]['nettingkey']) && !empty($rsHeader[0]['nettingkey'])) ? true : false;
        $isAPEmployeeCommission = (!empty($rsHeader[0]['refapemployeecommissionkey'])) ? true : false; 
        
        if($isnetting || $isAPEmployeeCommission){
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
        

        if (!empty($arrKeys)){
            $arrKeys = implode(',',$arrKeys);
            $rsAR = $ARObj->searchData('','',true,' and ' .$ARObj->tableName.'.pkey in ('.$arrKeys.') and ' .$ARObj->tableName.'.statuskey in (3,4) ' );
            if (!empty($rsAR)){
                $arrAR = array_column($rsAR,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br>'.implode(', ',$arrAR).'.'); 
            }
        }
        
        $rsAR = $ARObj->searchData('','',true,' and ' .$ARObj->tableName.'.pkey in ('.$arrKeys.') ' );
        $trDate =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        for($i=0;$i<count($rsAR);$i++){
            $arDate = $this->formatDBDate($rsAR[$i]['trdate'],'d / m / Y');
            $dateDiff = $this->dateDiff($trDate,$arDate);
            if($dateDiff > 0)
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['arPayment'][4]); 
        }
        
         if (ADV_FINANCE && TEST_VOUCHER){
                for($i=0;$i<count($rsPayment); $i++){ 
                    // cek kalo customerkey sudah beda
                    
                    if ($rsPayment[$i]['voucheremployeekey'] <> $customerkey && $rsPayment[$i]['voucheremployeekey'] <> 0)
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][3]); 
                    else if ($rsPayment[$i]['voucheroutstanding'] < $rsPayment[$i]['amount'])
                        // cek kalo outstanding masih cukup
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][4]); 
                    
                    else if ($rsPayment[$i]['voucherstatuskey'] <> TRANSACTION_STATUS['konfirmasi'])
                         $this->addErrorLog(false,'<b>'.$rsPayment[$i]['vouchercode']. '</b>. ' . $this->errorMsg['cashBank'][5]); 
                 
                }  
        }
        
	 } 
    
    function validateForm($arr,$pkey = ''){
        
		$ARObj = $this->getARObj();
		$arrayToJs = array();
        
		$employeekey = $arr['hidEmployeeKey'];  
		$arrARkey = $arr['hidARKey']; 
		$arrAmount = $arr['amount'];
		//$arrPick = $arr['chkPick']; 
        $trDate = $arr['trDate'];

        $arrDetailKey = array(); 
        
        $arrAR = array();
        $rsAR = $ARObj->searchData('','',true, ' and '.$ARObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrARkey)).') '); 
        $arrAR = array_column($rsAR, 'code', 'pkey');
        $arrAREmployee = array_column($rsAR, 'customerkey', 'pkey');
        $arrDate = array_column($rsAR, 'trdate', 'pkey');
        
        
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
			
		if(empty($employeekey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]);
		}
		   
        $hasAR = false; 
		for($i=0;$i<count($arrARkey);$i++) { 
            if (!empty($arrARkey[$i]))  //  && !empty($arrPick[$i]) 
                $hasAR = true;  
            
            if (in_array($arrARkey[$i],$arrDetailKey)){   
                $this->addErrorList($arrayToJs,false, $arrAR[$arrARkey[$i]].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrDetailKey, $arrARkey[$i]);
            }
            
        }
        
        if (!$hasAR)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ar'][1]); 	
        
		for($i=0;$i<count($arrARkey);$i++) {  
			if (!empty($arrARkey[$i]) && ($this->unFormatNumber($arrAmount[$i]) <= 0)){  
				$this->addErrorList($arrayToJs,false,$arrAR[$arrARkey[$i]]. '. ' . $this->errorMsg['arPayment'][2]); 
			}
            
            if (!empty($arrARkey[$i]) && $arrAREmployee[$arrARkey[$i]] <> $employeekey){
                $this->addErrorList($arrayToJs,false,$arrAR[$arrARkey[$i]]. '. ' . $this->errorMsg['ar'][5]); 
            }
            $arDate = $this->formatDBDate($arrDate[$arrARkey[$i]],'d / m / Y ');
            $dateDiff = $this->dateDiff($trDate,$arDate);

            if($dateDiff > 0)
               $this->addErrorList($arrayToJs,false,'<strong>'.$arrAR[$arrARkey[$i]].'</strong>.'. $this->errorMsg['arPayment'][4]);

		}
		
		return $arrayToJs;
	 }
	 
    
	function updateGL($rs, $rsPayment){
		// nanti perlu diupdate utk $rsPayment
		
        if (!USE_GL) return;
        
        if ($rs[0]['overwriteGL'] == 1) return;
        
		$warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal(); 
		$employee = new Employee();
         
        $warehousekey = $rs[0]['warehousekey'];
         
		if(ADV_FINANCE && TEST_VOUCHER) 
			$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey']);
		
        
        // COA AR 
        $rsEmployee = $employee->getDataRowById($rs[0]['customerkey']); 
        $employeCOAKey = $rsEmployee[0]['cashbankcoakey'];
        $employeARCOAKey = $rsEmployee[0]['arcoakey'];

        $rsCOAEmployeeAR = $coaLink->getCOALink ('employeear', $warehouse->tableName, $warehousekey); 
        $employeARCOAKey = (empty($employeARCOAKey)) ?  $rsCOAEmployeeAR[0]['coakey'] : $employeARCOAKey; 
         
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =   $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0; 
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
        
/*        $desc = array();
        array_push($desc,$this->ucFirst($this->lang['employeeAccountsReceivablePayment']));
        
 	    $rsDetail = $this->getDetailWithRelatedInformation($rs[0]['pkey']);
        foreach($rsDetail as $detailRow)
             array_push($desc,$detailRow['arcode']);
        
        $desc = implode('. ', $desc);
        $desc .= '.';*/
         
		$arr['trDesc'] =  $this->lang['employeeARPayment']. ' '.$rsEmployee[0]['name'];
        
		$temp = -1;
		$rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);   
        for($i=0;$i<count($rsPayment); $i++){ 
            
            if(ADV_FINANCE && TEST_VOUCHER){
//				$rsPayment = $this->getPaymentVoucherDetail($rs[0]['pkey']); // harusnya udah gk perlu
				
				$rsCashBank = $cashBank->getDataRowById($rsPayment[$i]['cashbankvoucherkey']);
				$rsCOA = $chartOfAccount->getDataRowById($rsCashBank[0]['coakey']);
				$paymentcoakey = $rsCOA[0]['countercoakey']; 
                
			}else{ 
                 $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
                 $paymentcoakey = $rsCOA[0]['coakey']; 
            }
            
            $temp++;
            $arr['hidCOAKey'][$temp] = $paymentcoakey;
            $arr['debit'][$temp] = $rsPayment[$i]['amount']; 
            $arr['credit'][$temp] = 0; 
            $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
            $arr['debitSource'][$temp] = $rsPayment[$i]['amount']; 
            $arr['creditSource'][$temp] = 0; 
            $arr['rate'][$temp] = 1 ; 
            $arr['refCashBankKey'][$temp] = $rsPayment[$i]['cashBankKey'];  // perlu dicek gk logol sama atau gk
        }
		    
        // piutang karyawan
        $temp++; 
        $arr['hidCOAKey'][$temp] = $employeARCOAKey;
        $arr['debit'][$temp] = 0;  
        $arr['credit'][$temp] = $rs[0]['totalreceived']; 
        $arr['trdescDetail'][$temp] = $rs[0]['code']; 
        $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
        $arr['debitSource'][$temp] = 0; 
        $arr['creditSource'][$temp] = $rs[0]['totalreceived']; 
        $arr['rate'][$temp] = 1 ; 
        $arr['refCashBankKey'][$temp] = '';  
		   

        //selisih pembayaran   
        $temp++; 
        if ($rs[0]['balance'] < 0){  
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = abs($rs[0]['balance']); 
            $arr['credit'][$temp] = 0;  
            $arr['debitSource'][$temp] = abs($rs[0]['balance']); 
            $arr['creditSource'][$temp] = 0;
        }else{ 
            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = abs($rs[0]['balance']);
            $arr['debitSource'][$temp] = 0; 
            $arr['creditSource'][$temp] =  abs($rs[0]['balance']);
        } 
        
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey']; 
        $arr['selCurrencyKey'][$temp] = CURRENCY['idr'] ; 
        $arr['rate'][$temp] = 1 ; 
        $arr['refCashBankKey'][$temp] = '';  
          
  
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);
    }    

    function normalizeParameter($arrParam, $trim=false){ 
         
        $arrParam['hidCustomerKey'] = $arrParam['hidEmployeeKey']; 
        $arrParam = parent::normalizeParameter($arrParam);  

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
            $arrParam['paymentMethodValue'] = array_values($arrParam['paymentMethodValue']); 
            $arrParam['hidDetailPaymentKey'] = array_values($arrParam['hidDetailPaymentKey']); 
        }else{  
            $arrParam['selPaymentMethod'] = array('0' => -1);
            $arrParam['paymentMethodValue'] = array('0' => $arrParam['grandtotal']);
            $arrParam['hidDetailPaymentKey'] = array('0' => 0);
            $arrParam['totalPayment'] = $arrParam['grandtotal'];
            $arrParam['balance'] = 0; 
        } 
          
        return $arrParam;
    }
   
 
    
    function getARObj(){
        return  new AREmployee();
    }
	
    function getBussinessPartnerObj(){
        return new Employee();    
    }
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
      $arObj = $this->getARObj();
        
      $rsRrealizationKey = $this->getTableKeyAndObj($arObj->tableCashBankRealization, array('key'));
         
      $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$arObj->tableName.'.code as arcode ,
                '.$arObj->tableName.'.refcode ,
                '.$arObj->tableName.'.refdate, 
                '.$arObj->tableCashBankRealization.'.refcode as reftranscode,
                '.$arObj->tableCashBankRealization.'.refcode2 as reftranscode2,
                '.$arObj->tableCashBankRealization.'.refcode3 as reftranscode3,
                '.$arObj->tableRefCustomer.'.name as customername
			  from
			  	'.$this->tableNameDetail .',
                '.$arObj->tableName.' 
                    left join '.$arObj->tableCashBankRealization.' on 
                        '.$arObj->tableName . '.reftabletype = '.$this->oDbCon->paramString($rsRrealizationKey['key']).' and
                        '.$arObj->tableCashBankRealization.'.pkey ='.$arObj->tableName . '.refheaderkey 
                    left join '.$arObj->tableRefCustomer.' on  '.$arObj->tableCashBankRealization . '.customerkey =  '.$arObj->tableRefCustomer . '.pkey
                    left join '.$arObj->tableType .' on  '.$arObj->tableName.'.artype = ' . $arObj->tableType .'.pkey 
			  where
			  	'. $this->tableNameDetail .'.arkey = '.$arObj->tableName.'.pkey and
			  	'. $this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').') ';
         
       
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   } 
    	     
    function afterStatusChanged($rsHeader){ 
          
        $ARObj = $this->getARObj();
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
        for($i=0;$i<count($rsDetail); $i++){  
           $ARObj->updateAROutstanding($rsDetail[$i]['arkey']); 
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
		  
        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }
    
    function afterAddDataOnCopy($pkey, $oldkey){  
      
    }
    

    
}

?>