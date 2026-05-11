<?php

class APEmployeePayment extends APPayment{  
  
    function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ap_employee_payment_header';
		$this->tableNameDetail = 'ap_employee_payment_detail';
		$this->tableEmployee = 'employee';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tablePayment= 'ap_employee_payment';
	    $this->tableAP = 'ap_employee';
        $this->tableDownpaymentDetail = 'ap_employee_downpayment'; // harusnya gk aka npernah kepake
        $this->tableDownpayment = 'employee_downpayment'; // harusnya gk aka npernah kepake
		$this->tableCost= 'ap_employee_cost';
        $this->tablePaymentMethod = 'payment_method';
		$this->uploadFileFolder = 'ap-employee-payment/';  

        $this->isTransaction = true;
        
		$this->securityObject = 'APEmployeePayment';
        
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['apkey'] = array('hidAPKey');
        $this->arrDataDetail['outstanding'] = array('outstanding','number');
        $this->arrDataDetail['amount'] = array('amount', array('datatype' => 'number','mandatory'=>true));
        //$this->arrDataDetail['discount'] = array('discount','number');
        //$this->arrDataDetail['taxamount'] = array('taxPPH','number');
       
        $arrPaymentDetail = array(); 
        $arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $arrPaymentDetail['amount'] = array('paymentMethodValue',array('datatype' => 'number','mandatory'=>true));
        $arrPaymentDetail['paymentkey'] = array('selPaymentMethod',array('mandatory'=>true)); 
       

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
		$this->arrData['nettingkey'] = array('nettingkey');
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['supplierkey'] = array('hidSupplierKey');	
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trnotes'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['grandtotal'] = array('grandtotal','number');
        $this->arrData['totalpayment'] = array('totalPayment','number');
        //$this->arrData['totaldiscount'] = array('totalDiscount','number');
        $this->arrData['balance'] = array('balance','number');
        $this->arrData['totalpaid'] = array('totalPaid','number');
		$this->arrData['islinked'] = array('islinked');
        $this->arrData['usedateperiod'] = array('chkDatePeriod');
        $this->arrData['startdateperiod'] = array('trStartDate','date');
        $this->arrData['enddateperiod'] = array('trEndDate','date');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
              
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'totalpaid', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
    
            
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/apEmployeePayment'));
        
        $this->includeClassDependencies(array(
                  'AP.class.php', 
                  'APEmployee.class.php', 
                  'APPayment.class.php', 
                  'ChartOfAccount.class.php', 
                  'COALink.class.php', 
                  'Employee.class.php', 
                  'Warehouse.class.php',
                  'PaymentMethod.class.php'
        
        ));  

        $this->overwriteConfig();
	}
	
    function getQuery(){
		
		$sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableEmployee.'.name as employeename,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname
			FROM 
                '.$this->tableName.', 
                '.$this->tableStatus.',  
                '.$this->tableEmployee.',  
                '.$this->tableWarehouse.'
			WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
				  '.$this->tableName.'.supplierkey = '.$this->tableEmployee.'.pkey  
		' .$this->criteria ;
        
        return $sql;
	}
     
 
	function reCountGrandtotal($arrParam){

		$grandtotal = 0;
		$amount = 0;
		$discount = 0;
		$pph = 0;

		$arrAPkey = $arrParam['hidAPKey'];
		$arrAmount = $arrParam['amount'];
		//$arrPick = $arrParam['chkPick']; 

		$arrAPDetail = array(); 

		for ($i=0;$i<count($arrAPkey);$i++){

			$arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);
			if ( empty($arrAPkey[$i]) || empty($arrAmount[$i]) )  // || empty($arrPick[$i])
				continue;

			$amount += $this->unFormatNumber($arrAmount[$i]);

		} 


		$totalpaid = $amount;

		$grandtotal = $amount;
		$balance = 0;
		$totalPayment = 0; 
		$payment = $arrParam['paymentMethodValue'];
		for($i=0;$i<count($payment);$i++){
			$totalPayment += $this->unFormatNumber($payment[$i]);
		} 
		$balance = $totalPayment  - $grandtotal;

		$reCountResult = array();
		$reCountResult['totalPaid'] = $totalpaid;
		$reCountResult['totalPayment'] = $totalPayment;
		$reCountResult['grandtotal'] = $grandtotal;
		$reCountResult['balance'] = $balance;

		return $reCountResult;
               
				
	}
 
	
	function validateForm($arr,$pkey = ''){
        
		$APObj = $this->getAPObj();
        
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$employeekey = $arr['hidEmployeeKey'];  
		$arrAPkey = $arr['hidAPKey']; 
		$arrAmount = $arr['amount'];
		$arrOutstanding= $arr['outstanding'];
        $trDate = $arr['trDate'];
		//$arrPick = $arr['chkPick']; 

        
        $arrDetailKey = array(); 
        
        $arrAP = array();
        $rsAP = $APObj->searchData('','',true, ' and '.$APObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrAPkey)).') '); 
        $arrAP = array_column($rsAP, 'code', 'pkey');
        $arrAPEmployee = array_column($rsAP, 'supplierkey', 'pkey');
        $arrDate = array_column($rsAP, 'trdate', 'pkey');
        
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
                
                if ($arrAPEmployee[$arrAPkey[$i]] <> $employeekey) 
                    $this->addErrorList($arrayToJs,false,$arrAP[$arrAPkey[$i]]. '. ' . $this->errorMsg['ap'][5]); 
                
                
                $apDate = $this->formatDBDate($arrDate[$arrAPkey[$i]],'d / m / Y');
                $dateDiff = $this->dateDiff($trDate,$apDate);
                
                if($dateDiff > 0)
                    $this->addErrorList($arrayToJs,false,'<strong>'.$arrAP[$arrAPkey[$i]].'</strong>.'. $this->errorMsg['apPayment'][4]);
            } 
		}
				
        return $arrayToJs;
	 }
    
    /*function afterStatusChanged($rsHeader){ 
        
		$APObj = $this->getAPObj();
         
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']);
  
        for($i=0;$i<count($rsDetail); $i++){   
           $APObj->updateAPEmployeeOutstanding($rsDetail[$i]['apkey']); 
        }   
        
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }*/
    
	  
	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $employeekey =  $rsHeader[0]['supplierkey'];
        
		$coaLink = new COALink();
        $warehouse = new Warehouse();  
        $ap = $this->getAPObj();
         
        $rsPayment = $this->getPaymentMethodDetail($id); 
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
		//update jurnal umum 
        $this->updateGL($rsHeader,$rsPayment);
	} 
    
    // kalo mau diaktifin, harus tambah validasi islink gk boleh dihapus
    //function validateCancel($rsHeader, $autoChangeStatus = false){ 
    //     
    //    $id = $rsHeader[0]['pkey'];
    //    
    //    $ap = $this->getAPObj();
    //     
		  //  
	 //} 
	 
 
	function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey'];
		
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
        $employee = new Employee();
		
        $warehousekey = $rs[0]['warehousekey'];
		$rsEmployee = $employee->getDataRowById($rs[0]['supplierkey']); 
        $employeCOAKey = $rsEmployee[0]['cashbankcoakey'];
        $employeAPCOAKey = $rsEmployee[0]['apcoakey'];

        $rsCOAEmployeeAP = $coaLink->getCOALink ('employeeap', $warehouse->tableName, $warehousekey); 
        $employeAPCOAKey = (empty($employeAPCOAKey)) ?  $rsCOAEmployeeAP[0]['coakey'] : $employeAPCOAKey; 
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0;  
		$arr['trDesc'] = $this->lang['employeeAPPayment']. ' '.$rsEmployee[0]['name'];
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
		$temp = -1;
        $rsPayment = $this->getPaymentMethodDetail($rs[0]['pkey']);  
        for($i=0;$i<count($rsPayment); $i++){ 
             $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rsPayment[$i]['paymentkey']); 
             $temp++;
             $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
             $arr['debit'][$temp] = 0; 
             $arr['credit'][$temp] = $rsPayment[$i]['amount'];  
		}
        
		 
        //selisih pembayaran   
        $temp++; 
        if ($rs[0]['balance'] < 0){ 
            $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = abs($rs[0]['balance']); 
        }else{ 
            $rsCOA = $coaLink->getCOALink ('othercost', $warehouse->tableName,$warehousekey, 0); 
            $arr['debit'][$temp] = abs($rs[0]['balance']); 
            $arr['credit'][$temp] = 0; 
        }

        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        
        
        $temp++; 
		$arr['hidCOAKey'][$temp] =  $employeAPCOAKey;
		$arr['debit'][$temp] = $rs[0]['totalpaid']; 
		$arr['credit'][$temp] = 0;
  
        
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);
	}
	 
	
	
	function getDetailPaymentByAPKey($apkey,$criteria = ''){
		$sql = 'select 
					'. $this->tableNameDetail.'.* 
				from 
					'. $this->tableNameDetail.','. $this->tableName.'  
				where 
					'. $this->tableNameDetail.'.refkey = '. $this->tableName.'  .pkey and
					'. $this->tableNameDetail.'.apkey = ' .$this->oDbCon->paramString($apkey).' and
				    ('. $this->tableName.'.statuskey = 2 or '. $this->tableName.'.statuskey = 3)
				'; 
		
		if(!empty($criteria))
            $sql .= $criteria;   
        
        $sql .= ' order by  pkey asc'; 
					  
		return $this->oDbCon->doQuery($sql);
	} 
    
    
    function getAPObj(){
        return new APEmployee();
    }
    
    function getBussinessPartnerObj(){
        return new Employee();    
    }
 
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
      $arObj = $this->getAPObj();
        
      $rsRrealizationKey = $this->getTableKeyAndObj($arObj->tableCashBankRealization, array('key'));
         
      $sql = 'select
	   			'.$this->tableNameDetail .'.*,
                '.$arObj->tableName.'.code as apcode ,
                '.$arObj->tableName.'.refcode ,
                '.$arObj->tableName.'.refdate, 
                '.$arObj->tableCashBankRealization.'.refcode as reftranscode,
                '.$arObj->tableCashBankRealization.'.refcode2 as reftranscode2,
                '.$arObj->tableCashBankRealization.'.refcode3 as reftranscode3,
                '.$arObj->tableCustomer.'.name as customername
			  from
			  	'.$this->tableNameDetail .',
                '.$arObj->tableName.' 
                    left join '.$arObj->tableCashBankRealization.' on 
                        '.$arObj->tableName . '.reftabletype = '.$this->oDbCon->paramString($rsRrealizationKey['key']).' and
                        '.$arObj->tableCashBankRealization.'.pkey ='.$arObj->tableName . '.refheaderkey 
                    left join '.$arObj->tableCustomer.' on  '.$arObj->tableCashBankRealization . '.customerkey =  '.$arObj->tableCustomer . '.pkey
                    left join '.$arObj->tableType .' on  '.$arObj->tableName.'.aptype = ' . $arObj->tableType .'.pkey 
			  where
			  	'. $this->tableNameDetail .'.apkey = '.$arObj->tableName.'.pkey and
			  	'. $this->tableNameDetail .'.refkey in('.$this->oDbCon->paramString($pkey,',').') ';
         
       
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   } 
    
    
    function normalizeParameter($arrParam, $trim=false){ 
        $arrParam = parent::normalizeParameter($arrParam); 
        $arrParam['hidSupplierKey'] = $arrParam['hidEmployeeKey']; 
		$this->removeUnCheckRows($arrParam,$this->arrDataDetail);
		
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
            $arrParam['selPaymentMethod'] = array();
            $arrParam['paymentMethodValue'] = array();
            $arrParam['hidDetailPaymentKey'] = array(); 
        }
         
        $reCountResult = $this->reCountGrandtotal($arrParam);

        $arrParam['totalPaid'] = $reCountResult['totalPaid'];
        $arrParam['grandtotal'] = $reCountResult['grandtotal'];
        $arrParam['totalPayment'] = $reCountResult['totalPayment'];
        $arrParam['balance'] = $reCountResult['balance'];
		
		if( $isnetting ){  
            $arrParam['selPaymentMethod'] = array('0' => -1);
            $arrParam['paymentMethodValue'] = array('0' => $arrParam['grandtotal']);
            $arrParam['hidDetailPaymentKey'] = array('0' => 0);
            $arrParam['totalPayment'] = $arrParam['grandtotal'];
            $arrParam['balance'] = 0; 
         }
          
        return $arrParam;
    }  
    
        	     
    function afterStatusChanged($rsHeader){ 
        
		$APObj = $this->getAPObj();
         
        $rsDetail = $this->getDetailById($rsHeader[0]['pkey']); 
        for($i=0;$i<count($rsDetail); $i++){   
           $APObj->updateAPOutstanding($rsDetail[$i]['apkey']); 
        }   
         
    }
    
    function afterAddDataOnCopy($pkey, $oldkey){  
      
    }
    
     
}

?>
