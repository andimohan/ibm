<?php

class ARAPEmployeeNetting extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'arap_employee_netting_header';
		$this->tableNameDetail = 'arap_employee_netting_ar_detail';
		$this->tableAPDetail = 'arap_employee_netting_ap_detail';
		$this->tableEmployee = 'employee';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tablePayment= 'ar_employee_payment';
        $this->tableCost = 'ar_employee_cost';
        $this->tableItem = 'item';
		$this->tableAR = 'ar_employee';
		$this->tableCustomer = 'customer';
        $this->tablePaymentMethod = 'payment_method'; 
		$this->tableCashBankRealization = 'cash_bank_realization_header';  
        $this->isTransaction = true;
       
        $this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail, $this->tablePayment,$this->tableCost);
		 
		$this->securityObject = 'ARAPEmployeeNetting';
       
        $this->arrDataDetail = array(); 
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['arkey'] = array('hidARKey',array('mandatory'=>true));
        $this->arrDataDetail['outstanding'] = array('arOutstanding',array('datatype' => 'number','mandatory'=>true));
        $this->arrDataDetail['amount'] = array('arAmount',array('datatype' => 'number','mandatory'=>true));

        $this->arrAPDetail = array(); 
        $this->arrAPDetail['pkey'] = array('hidDetailAPKey');
        $this->arrAPDetail['refkey'] = array('pkey', 'ref');
        $this->arrAPDetail['apkey'] = array('hidAPKey',array('mandatory'=>true));
        $this->arrAPDetail['outstanding'] = array('apOutstanding',array('datatype' => 'number','mandatory'=>true));
        $this->arrAPDetail['amount'] = array('apAmount',array('datatype' => 'number','mandatory'=>true));
         

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $this->arrAPDetail, 'tableName' => $this->tableAPDetail));
          
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trnotes'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['totalar'] = array('totalARAmount','number');
        $this->arrData['totalap'] = array('totalAPAmount','number'); 
        $this->arrData['usedateperiod'] = array('chkDatePeriod');
        $this->arrData['startdateperiod'] = array('trStartDate','date');
        $this->arrData['enddateperiod'] = array('trEndDate','date');
        $this->arrData['grandtotalar'] = array('grandtotalARAmount','number');
        $this->arrData['grandtotalap'] = array('grandtotalAPAmount','number'); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 80, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename', 'default'=>true, 'width' => 160));
	    //array_push($this->arrDataListAvailableColumn, array('code' => 'totalar','title' => 'totalAR','dbfield' => 'grandtotalar', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
	    //array_push($this->arrDataListAvailableColumn, array('code' => 'totalap','title' => 'totalAP','dbfield' => 'grandtotalap', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes','default'=>true, 'width' => 200)); 
      
       
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/arapEmployeeNetting'));
          
	   $this->includeClassDependencies(array(
                  'AP.class.php', 
                  'APEmployee.class.php', 
                  'APPayment.class.php', 
                  'APEmployeePayment.class.php', 
		   		  'AR.class.php', 
                  'AREmployee.class.php', 
                  'ARPayment.class.php', 
                  'AREmployeePayment.class.php', 
                  'ChartOfAccount.class.php', 
                  'COALink.class.php', 
                  'GeneralJournal.class.php', 
                  'Employee.class.php', 
                  'CashMovement.class.php', 
                  'Warehouse.class.php',
                  'Customer.class.php',
                  'Supplier.class.php'
        
        )); 
	   
	    $this->arap = array();
        $this->arapConstant['ar'] = array(  'obj' => new AREmployee(),
                                            'hidrefkey' => 'hidARKey',
                                            'refkey' => 'arkey',
                                            'hidrecipientkey' => 'hidEmployeeKey',
                                            'recipientkey' => 'employeekey',
                                            'reftax23' => 'prepaidtax23',
                                            'tableARAPDetail' => $this->tableNameDetail,
                                );
            
        $this->arapConstant['ap'] = array( 
                                        'obj' => new APEmployee(),
                                        'hidrefkey' => 'hidAPKey',
                                        'refkey' => 'apkey',
                                        'hidrecipientkey' => 'hidEmployeeKey',
                                        'recipientkey' => 'employeekey',
                                        'reftax23' => 'payabletax23',
                                        'tableARAPDetail' => $this->tableAPDetail,
                                );
       
        $this->overwriteConfig();
	}
	
	function getQuery(){
		
		$sql = '
			SELECT '.$this->tableName.'.* ,
			   '.$this->tableEmployee.'.name as employeename,
			   '.$this->tableWarehouse.'.name as warehousename,
			   '.$this->tableStatus.'.status as statusname
			FROM '.$this->tableStatus.',
                 '.$this->tableEmployee.', 
                  '.$this->tableName.', 
                  '.$this->tableWarehouse.'
			WHERE '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey and
				  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
				  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
		' .$this->criteria ;
        
        
        return $sql;
	}
	
	function reCountGrandtotal($arrParam){

				$grandtotalar = 0;
				$grandtotalap = 0;
				$totalar = 0;
				$totalap = 0;
				$taxar = 0;
				$taxap = 0;
				
				$arrARkey = $arrParam['hidARKey'];
				$arrAPkey = $arrParam['hidAPKey'];
				$arrARAmount = $arrParam['arAmount'];
				$arrAPAmount = $arrParam['apAmount'];
				$arrAROutstanding = $arrParam['arOutstanding'];
				$arrAPOutstanding = $arrParam['apOutstanding'];
				
				$arrARDetail = array(); 
				
				for ($i=0;$i<count($arrARkey);$i++){
					
				    $arrARAmount[$i] = $this->unFormatNumber($arrARAmount[$i]);
				    $arrAROutstanding[$i] = $this->unFormatNumber($arrAROutstanding[$i]);
					if ( empty($arrARkey[$i]) || empty($arrARAmount[$i]) || $arrARAmount[$i] < 0)  //  || empty($arrPick[$i]) 
						continue; 
					
				    $totalar +=  $arrARAmount[$i];
				}
        
                for ($i=0;$i<count($arrAPkey);$i++){
					
				    $arrAPOutstanding[$i] = $this->unFormatNumber($arrAPOutstanding[$i]);
				    $arrAPAmount[$i] = $this->unFormatNumber($arrAPAmount[$i]);
					if ( empty($arrAPkey[$i]) || empty($arrAPAmount[$i]) || $arrAPAmount[$i] < 0)  //  || empty($arrPick[$i]) 
						continue; 
					
				    $totalap +=  $arrAPAmount[$i];
				}  
        
                // total yg dilunasin
                $grandtotalar = $totalar;
                $grandtotalap = $totalap;
			 
				$reCountResult = array();
				$reCountResult['totalARAmount'] = $totalar;
				$reCountResult['totalAPAmount'] = $totalap;
				$reCountResult['grandtotalARAmount'] = $grandtotalar;
				$reCountResult['grandtotalAPAmount'] = $grandtotalap;
				
				return $reCountResult;
				
	}
	 
	function validateForm($arr,$pkey = ''){
        
		$ARObj = $this->getARObj();
		$APObj = $this->getAPObj();
            
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$employeekey = $arr['hidEmployeeKey'];  
		$arrARkey = $arr['hidARKey']; 
		$arrAPkey = $arr['hidAPKey']; 
		$arrARAmount = $arr['arAmount'];
		$arrAPAmount = $arr['apAmount'];
        $arrAROutstanding = $arr['arOutstanding'];
		$arrAPOutstanding = $arr['apOutstanding'];
		$grandTotalAR = $this->unFormatNumber($arr['grandtotalARAmount']);
		$grandTotalAP = $this->unFormatNumber($arr['grandtotalAPAmount']);
		//$arrPick = $arr['chkPick'];  
        
        if (empty($arrARkey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ar'][1]); 
        
        if (empty($arrAPkey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ap'][1]); 

        $arrDetailKey = array();
        $arrAPDetailKey = array();
        
        $arrAR = array();
        $rsAR = $ARObj->searchData('','',true, ' and '.$ARObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrARkey)).') '); 
        $arrAR = array_column($rsAR, 'code', 'pkey');
        $arrAREmployee = array_column($rsAR, 'customerkey', 'pkey');
		
        $arrAP = array();
        $rsAP = $APObj->searchData('','',true, ' and '.$APObj->tableName.'.pkey in ('.implode(',',$this->oDbCon->paramString($arrAPkey)).') '); 
        $arrAP = array_column($rsAP, 'code', 'pkey');
        $arrAPEmployee = array_column($rsAP, 'supplierkey', 'pkey');
            
         
		//validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
			
		if(empty($employeekey)) 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]); 
	 
        $hasAR = false; 
        for($i=0;$i<count($arrARkey);$i++) { 
            if ($arrAREmployee[$arrARkey[$i]] <> $employeekey) 
                $this->addErrorList($arrayToJs,false,$arrAR[$arrARkey[$i]]. '. ' . $this->errorMsg['ar'][5]); 
             
            
            if (!empty($arrARkey[$i])) $hasAR = true;  

            if (in_array($arrARkey[$i],$arrDetailKey))  
                $this->addErrorList($arrayToJs,false, $arrAR[$arrARkey[$i]].'. '.$this->errorMsg[215]); 	 
             else 
                array_push($arrDetailKey, $arrARkey[$i]); 
             
            
            if($arrARAmount[$i]>$arrAROutstanding[$i])
                $this->addErrorList($arrayToJs,false, $arrAR[$arrARkey[$i]].'. '.$this->errorMsg['arPayment'][2]); 
             
        }

        if (!$hasAR)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ar'][1]); 	

      
        $hasAP = false; 
		for($i=0;$i<count($arrAPkey);$i++) { 
            if ($arrAPEmployee[$arrAPkey[$i]] <> $employeekey) 
                $this->addErrorList($arrayToJs,false,$arrAP[$arrAPkey[$i]]. '. ' . $this->errorMsg['ap'][5]); 
            
			if (!empty($arrAPkey[$i]))  $hasAP = true;  
            
            if (in_array($arrAPkey[$i],$arrAPDetailKey)){   
                $this->addErrorList($arrayToJs,false, $arrAP[$arrAPkey[$i]].'. '.$this->errorMsg[215]); 	 
            }else{ 
                array_push($arrAPDetailKey, $arrAPkey[$i]);
            }
            
          
            if($arrAPAmount[$i]>$arrAPOutstanding[$i])
                $this->addErrorList($arrayToJs,false, $arrAP[$arrAPkey[$i]].'. '.$this->errorMsg['apPayment'][2]); 
         }
        
        if (!$hasAP)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['ap'][1]); 	
        
		
		return $arrayToJs;
	 }
	     
    function afterStatusChanged($rsHeader){ 
        
        /*$ARObj = $this->getARObj();
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
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); */
    }
    
	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $customerkey =  $rsHeader[0]['employeekey'];
        
		$coaLink = new COALink();
        $warehouse = new Warehouse();
        $ar = $this->getARObj();
        $ap = $this->getAPObj();
        
        // HARUS CEK ULANG OUTSTANDING SETIAP AR AP 
        
        
        /*if (USE_GL){ 

            $arrCOA = array();
            array_push($arrCOA, 'ar','ap', 'otherrevenue','othercost'); 
            for ($i=0;$i<count($arrCOA);$i++){
                $rsCOA = $coaLink->getCOALink ($arrCOA[$i], $warehouse->tableName,$rsHeader[0]['warehousekey'], 0); 
                if (empty($rsCOA))	
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$arrCOA[$i]. ' ' .$this->errorMsg['coa'][3]);
            }   
 
            for($i=0;$i<count($rsPayment); $i++){ 
                if ($rsPayment[$i]['amount'] > 0 ){ 
                    $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$rsHeader[0]['warehousekey'], $rsPayment[$i]['paymentkey']); 
                    if (empty($rsCOA))	
                        $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['coa'][3]); 
                }
            } 
              
        } */   
        
        $rsDetail = $this->getDetailARAP($this->arapConstant['ar'],$id);
        $arrKeys = array_column($rsDetail,'arkey');   
        // cek status piutang sudah lunas atau blm
        if (!empty($arrKeys)){ 
            $rsAR = $ar->searchData('','',true,' and ' .$ar->tableName.'.pkey in ('.$this->oDbCon->paramString($arrKeys,',').') and ' .$ar->tableName.'.statuskey in (3,4) ' );
            
            if (!empty($rsAR)){
                $arrAR = array_column($rsAR,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br>'.implode(', ',$arrAR).'. '.$this->errorMsg['ar'][6]); 
            }
        }
        
        $rsAPDetail = $this->getDetailARAP($this->arapConstant['ap'],$id);
        $arrAPKeys = array_column($rsAPDetail,'apkey');        
        // cek status hutang sudah lunas atau blm
        if (!empty($arrAPKeys)){ 
            $rsAP = $ap->searchData('','',true,' and ' .$ap->tableName.'.pkey in ('.$this->oDbCon->paramString($arrAPKeys,',').') and ' .$ap->tableName.'.statuskey in (3,4) ' );
            
            if (!empty($rsAP)){
                $arrAP = array_column($rsAP,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg[201].'<br>'.implode(', ',$arrAP).'. '.$this->errorMsg['ap'][6]); 
            }
        }
    }
	 
	function confirmTrans($rsHeader){  
        $this->updateARAP($rsHeader);
        
        $this->updateGL($rsHeader); 
	}
    
    function updateARAP($rsHeader){
         
		$id = $rsHeader[0]['pkey'];
        
        $rsDetailAR = $this->getDetailARAP($this->arapConstant['ar'],$id);
		$rsDetailAP = $this->getDetailARAP($this->arapConstant['ap'],$id);
         
        $this->arapConstant['ar']['rsARAPDetail'] = $rsDetailAR;
        $this->arapConstant['ap']['rsARAPDetail'] = $rsDetailAP;
            
        $totalAR = 0;
        $totalAP = 0;
        $totalLimit = 0;
        $totalOutstanding = 0;
        
         
        //ar
        for($i=0;$i<count($rsDetailAR);$i++)
            $totalAR += $rsDetailAR[$i]['amount'];
        
        for($i=0;$i<count($rsDetailAP);$i++)
            $totalAP += $rsDetailAP[$i]['amount'];
            
        $this->arapConstant['ar']['totalLimit'] = $totalAR;
        $this->arapConstant['ap']['totalLimit'] = $totalAP;
        
        $balance = $totalAR - $totalAP;
        
        if($balance > 0){
            $arapIndex = 'ap';
            $arapIndexCounter = 'ar';
        }else{
            $arapIndex = 'ar';
            $arapIndexCounter = 'ap';
        }
           
        $this->addPayment($rsHeader,$this->arapConstant[$arapIndex]);
         
        $arrDetail = array();
        $arapDetail = $this->arapConstant[$arapIndexCounter]['rsARAPDetail'];
          
        $refkey = $this->arapConstant[$arapIndexCounter]['refkey'];
        $totalLimit = $this->arapConstant[$arapIndex]['totalLimit'];
            
        for($i=0;$i<count($arapDetail);$i++){
            if($totalLimit<=0) break;
              
            $arrDetail[$i][$refkey] = $arapDetail[$i][$refkey]; 
            
            $amount = $totalLimit-$arapDetail[$i]['amount']; 
            $arrDetail[$i]['amount'] = ($amount >= 0) ? $arapDetail[$i]['amount'] : abs($totalLimit);
      
            $totalLimit -= $arrDetail[$i]['amount']; 
        }
         
        //lawan nya transaksi awal
        if(!empty($arrDetail)) {  
            $this->arapConstant[$arapIndexCounter]['rsARAPDetail'] = $arrDetail;
            $this->addPayment($rsHeader,$this->arapConstant[$arapIndexCounter]);
        }
    
    }       
    
    function updatePaymentWithTax($rsHeader){
        $id = $rsHeader[0]['pkey'];
        
        $rsDetailAR = $this->getDetailARAP($this->arapConstant['ar'],$id);
		$rsDetailAP = $this->getDetailARAP($this->arapConstant['ap'],$id);
        $this->arapConstant['ar']['rsARAPDetail'] = $rsDetailAR;
        $this->arapConstant['ap']['rsARAPDetail'] = $rsDetailAP;
    
        $this->addPayment($rsHeader,$this->arapConstant['ar']);  
        $this->addPayment($rsHeader,$this->arapConstant['ap']);
        
    }
    
     function addPayment($rsHeader,$arapConstant){
        $rsDetail = $arapConstant['rsARAPDetail'];
         
        if (empty($rsDetail)) return;
        
        $obj = $arapConstant['obj'];
        $paymentObj = $obj->getPaymentObj();
              
        $hidRefKey = $arapConstant['hidrefkey'];
        $refKey = $arapConstant['refkey'];
        $hidrecipientkey = $arapConstant['hidrecipientkey'];
        $recipientkey = $arapConstant['recipientkey']; 
            
        $arrParam = array();	
        $totalAmount = 0; 
         
        $arapkey = array_column($rsDetail,$refKey);
        
        $rsOutstanding = $obj->searchData('','',true, ' and ' .$obj->tableName.'.pkey in ('.$obj->oDbCon->paramString($arapkey,',').') ');
        $rsOutstanding = array_column($rsOutstanding,'outstanding','pkey');
           
        for($i=0;$i<count($rsDetail);$i++){   
            $arrParam['hidDetailKey'][$i] = 0;
            $arrParam[$hidRefKey][$i] = $rsDetail[$i][$refKey]; 
            
            $arrParam['chkPick'][$i] = 1;
            
            // outstanding setiap AP / AR harus dihitung ulang
            $arrParam['outstanding'][$i] = $rsOutstanding[$rsDetail[$i][$refKey]];
             
            $arrParam['amount'][$i] = $rsDetail[$i]['amount']; 
        }
         
         
        $arrParam['code'] = 'xxxxxx';
        $arrParam['islinked'] = 1; 
        $arrParam['nettingkey'] = $rsHeader[0]['pkey']; 
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
         
        $arrParam[$hidrecipientkey] = $rsHeader[0][$recipientkey];
        
        $arrParam['trDesc'] = $rsHeader[0]['code'];
        $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        $arrParam['chkDatePeriod'] = $rsHeader[0]['usedateperiod'];
        $arrParam['trStartDate'] = $this->formatDBDate($rsHeader[0]['startdateperiod'],'d / m / Y');
        $arrParam['trEndDate'] = $this->formatDBDate($rsHeader[0]['enddateperiod'],'d / m / Y');
        $arrParam['hidSaveAndProceed'] = 1;
        $arrParam['overwriteGL'] = 1;
        
        $arrayToJs = $paymentObj->addData($arrParam); 
   
        if (!$arrayToJs[0]['valid'])
            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 
     
    }

    function validateCancel($rsHeader,$autoChangeStatus = false){ 
            $id = $rsHeader[0]['pkey'];
            $ar = $this->getARObj();
            $arPayment= new AREmployeePayment();
            $apPayment= new APEmployeePayment();

/*
            //cek ad Prepaid yg ad bukti potongnya blm
            $rsARPayment = $arPayment->searchData('','',true,' and '.$arPayment->tableName.'.nettingkey = '.$this->oDbCon->paramString($id).' and ('.$arPayment->tableName.'.statuskey in (2,3) )');

            if(!empty($rsARPayment)){
                $arrAR = array_column($rsARPayment,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsARPayment[0]['code'] .'</strong>, '.  $this->errorMsg[203]);             } 
        
            $rsAPPayment = $apPayment->searchData('','',true,' and '.$apPayment->tableName.'.nettingkey = '.$this->oDbCon->paramString($id).' and ('.$apPayment->tableName.'.statuskey in (2,3) )');

            if(!empty($rsAPPayment)){
                $arrAP = array_column($rsAPPayment,'code');
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].'<br><strong>'.$rsAPPayment[0]['code'] .'</strong>, '.  $this->errorMsg[203]); 
            }
*/

    } 

    function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey']; 
        $cashMovement = new CashMovement();   
        $cashMovement->cancelMovement($id,$this->tableName); 

        $this->deleteARAPPayment($id); 


        if ($copy)
            $this->copyDataOnCancel($id);	  

        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
    }


function updateGL($rs){
        if (!USE_GL) return;
        
        $id = $rs[0]['pkey'];
        $warehousekey =  $rs[0]['warehousekey'];
        
        $apPayment = new APEmployeePayment();
        $arPayment = new AREmployeePayment();
        $employee = new Employee();
        $generalJournal = new GeneralJournal();
        $coaLink = new COALink();
        $warehouse = new Warehouse();
		$rsEmployee = $employee->getDataRowById($rs[0]['employeekey']); 
        
        $rsAPPayment = $apPayment->searchData($apPayment->tableName.'.nettingkey', $id, true, ' and '.$apPayment->tableName.'.statuskey <> 4' ); 
        $rsAPPaymentDetail = array(); 
        $apCOAKey = 0;
        if((!empty($rsAPPayment))){
            $rsAPPaymentDetail = $apPayment->getDetailById($rsAPPayment[0]['pkey']);
			 
			$employeAPCOAKey = $rsEmployee[0]['apcoakey'];
			$rsCOAEmployeeAP = $coaLink->getCOALink ('employeeap', $warehouse->tableName, $warehousekey); 
			$apCOAKey = (empty($employeAPCOAKey)) ?  $rsCOAEmployeeAP[0]['coakey'] : $employeAPCOAKey; 
        }
         
        
        $rsARPayment = $arPayment->searchData($arPayment->tableName.'.nettingkey', $id, true, ' and '.$arPayment->tableName.'.statuskey <> 4' );
        $rsARPaymentDetail = array(); 
        $arCOAKey = 0;
        if((!empty($rsARPayment))){
            $rsARPaymentDetail = $arPayment->getDetailById($rsARPayment[0]['pkey']);
			$employeARCOAKey = $rsEmployee[0]['arcoakey'];
        	$rsCOAEmployeeAR = $coaLink->getCOALink ('employeear', $warehouse->tableName, $warehousekey); 
        	$arCOAKey = (empty($employeARCOAKey)) ?  $rsCOAEmployeeAR[0]['coakey'] : $employeARCOAKey; 
        }
        
             
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =   $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0; 
        $arr['trDesc'] = $this->lang['employeeARAPNetting'].' '.$rsEmployee[0]['name'];
        $arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
		$temp = -1; 

		$totalPayment = min(array($rs[0]['totalar'], $rs[0]['totalap']));

		$temp++;
		$arr['hidCOAKey'][$temp] = $apCOAKey;
		$arr['debit'][$temp] = $totalPayment; 
		$arr['credit'][$temp] = 0;  

		$temp++;
		$arr['hidCOAKey'][$temp] = $arCOAKey;
		$arr['debit'][$temp] = 0; 
		$arr['credit'][$temp] = $totalPayment;   
 
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']); 

    }
    
    
      
    function deleteARAPPayment($id){ 
          
        $arPayment = new AREmployeePayment(); 
        $apPayment = new APEmployeePayment(); 
        
        $rsAR = $arPayment->searchData('','',true,' and '.$arPayment->tableName.'.nettingkey = '.$this->oDbCon->paramString($id));    
        for($i=0;$i<count($rsAR);$i++) { 
            $arrayToJs = $arPayment->changeStatus($rsAR[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        } 
        
        $rsAP = $apPayment->searchData('','',true,' and '.$apPayment->tableName.'.nettingkey = '.$this->oDbCon->paramString($id));    
        for($i=0;$i<count($rsAP);$i++) { 
            $arrayToJs = $apPayment->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
          
      }


    function getARObj(){
        return new AREmployee();
    }
        
    function getAPObj(){
        return new APEmployee();
    }
    
    function getPrepaidTaxObj(){
       // return new ARPrepaidTax23();
    }
	
    
    function normalizeParameter($arrParam, $trim = false){
         
        
        $arrParam['trStartDate'] = (!empty($arrParam['trStartDate'])) ? $arrParam['trStartDate'] : DEFAULT_EMPTY_DATE;  
        $arrParam['trEndDate'] = (!empty($arrParam['trEndDate'])) ? $arrParam['trEndDate'] : DEFAULT_EMPTY_DATE;
        $arrParam['hidARKey'] = (!empty($arrParam['hidARKey'])) ? $arrParam['hidARKey'] : array();
        $arrParam['hidAPKey'] = (!empty($arrParam['hidAPKey'])) ? $arrParam['hidAPKey'] : array();
        
        // ini kayanya harusnya dihitung ulang
        $arrParam['arAmount'] = (!empty($arrParam['arAmount'])) ? $arrParam['arAmount'] : array();
        $arrParam['apAmount'] = (!empty($arrParam['apAmount'])) ? $arrParam['apAmount'] : array();
        
        $reCountResult = $this->reCountGrandTotal($arrParam); 
        $arrParam['totalARAmount'] = $reCountResult['totalARAmount'];
        $arrParam['totalAPAmount'] = $reCountResult['totalAPAmount'];
        $arrParam['grandtotalARAmount'] = $reCountResult['grandtotalARAmount'];
        $arrParam['grandtotalAPAmount'] = $reCountResult['grandtotalAPAmount'];
        $arrParam = parent::normalizeParameter($arrParam,true);  
       
        return $arrParam;
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
			  	'. $this->tableNameDetail .'.refkey = '.$this->oDbCon->paramString($pkey);
         
       
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   }
    
    function getDetailARAP($arrConstant, $pkey,$criteria=''){ 
      $obj = $arrConstant['obj'];
      $tableAPDetail = $arrConstant['tableARAPDetail'];
        
      $rsKey = $this->getTableKeyAndObj($this->tableCashBankRealization, array('key'));
      //   jgn pake $obj->tableCustomer, karena di AR AP bisa beda
      $sql = 'select
	   			'.$tableAPDetail.'.*,
                '.$obj->tableName.'.code as arcode ,
                '.$obj->tableName.'.refcode ,
                '.$obj->tableName.'.refcode2 ,
                '.$obj->tableName.'.refdate,
                '.$obj->tableCashBankRealization.'.refcode as reftranscode,
                '.$obj->tableCashBankRealization.'.refcode2 as reftranscode2,
                '.$obj->tableCashBankRealization.'.refcode3 as reftranscode3,
                '.$this->tableCustomer.'.name as customername
			  from
			  	'.$tableAPDetail.',
                '.$obj->tableName.'
                    left join '.$obj->tableCashBankRealization.' on '.$obj->tableName . '.reftabletype = '.$obj->oDbCon->paramString($rsKey['key']).' and
                    '.$obj->tableCashBankRealization.'.pkey ='.$obj->tableName . '.refheaderkey 
                    left join '.$this->tableCustomer.' on  '.$obj->tableCashBankRealization . '.customerkey =  '.$this->tableCustomer . '.pkey
			  where
			  	'.$tableAPDetail.'.'.$arrConstant['refkey'].' = '.$obj->tableName.'.pkey and
			  	'.$tableAPDetail.'.refkey = '.$obj->oDbCon->paramString($pkey);
          
        $sql .= $criteria; 
   
        return $this->oDbCon->doQuery($sql);
   }
     
}
?>
