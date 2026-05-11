<?php

class APEmployeeCommissionPayment extends BaseClass{  
  
    function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ap_employee_commission_payment_header';
		$this->tableNameDetail = 'ap_employee_commission_payment_detail';
		$this->tableEmployee = 'employee';
		$this->tableStatus = 'transaction_status';
		$this->tableWarehouse = 'warehouse'; 
		$this->tablePayment= 'ap_employee_commission_payment';
		$this->tableCost= 'ap_employee_commission_cost';
        $this->tableAREmployeeDetail = 'ap_employee_commission_ar';
        $this->tableAREmployee = 'ar_employee';
        $this->tableItem = 'cost_cash_out';
	    $this->tableWorkOrder = 'trucking_service_work_order';
        $this->tableDownpaymentDetail = 'ap_employee_commission_downpayment';// harusnya gk aka npernah kepake
        $this->tableDownpayment = 'employee_downpayment'; // harusnya gk aka npernah kepake
	    $this->tableAP = 'ap';
        $this->tablePaymentMethod = 'payment_method';
        $this->tableCar = 'car';
        $this->tableItemService = 'item'; // soalnya diatas udah ad yg pake table item
        $this->tableFile = '';
		$this->uploadFileFolder = 'ap-employee-commission-payment/';

        $this->isTransaction = true;
        
		$this->securityObject = 'APEmployeeCommissionPayment';
        
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
       
       
     /*   $arrDownpaymentDetail = array(); 
        $arrDownpaymentDetail['pkey'] = array('hidDetailDownpaymentKey');
        $arrDownpaymentDetail['refkey'] = array('pkey', 'ref');
        $arrDownpaymentDetail['amount'] = array('downpaymentAmount',array('datatype' => 'number','mandatory'=>true));
        $arrDownpaymentDetail['downpaymentkey'] = array('hidDownpaymentKey',array('mandatory'=>true)); 
        */

  	    $arrCostDetail = array(); 
        $arrCostDetail['pkey'] = array('hidDetailCostKey');
        $arrCostDetail['refkey'] = array('pkey', 'ref');
        $arrCostDetail['amount'] = array('costAmount',array('datatype' => 'number','mandatory'=>true));
        $arrCostDetail['costkey'] = array('hidCostKey',array('mandatory'=>true)); 

        
        $arrAREmployeeDetail = array(); 
        $arrAREmployeeDetail['pkey'] = array('hidDetailAREmployeeKey');
        $arrAREmployeeDetail['refkey'] = array('pkey', 'ref');
        $arrAREmployeeDetail['amount'] = array('arEmployeeAmount',array('datatype' => 'number','mandatory'=>true));
        $arrAREmployeeDetail['aremployeekey'] = array('selAREmployee',array('mandatory'=>true)); 
  	           
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        array_push($arrDetails, array('dataset' => $arrPaymentDetail, 'tableName' => $this->tablePayment));
       // array_push($arrDetails, array('dataset' => $arrDownpaymentDetail, 'tableName' => $this->tableDownpaymentDetail));
        array_push($arrDetails, array('dataset' => $arrCostDetail, 'tableName' => $this->tableCost));
        array_push($arrDetails, array('dataset' => $arrAREmployeeDetail, 'tableName' => $this->tableAREmployeeDetail));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['employeekey'] = array('hidEmployeeKey');	
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
        $this->arrData['totalaremployee'] = array('totalAREmployee','number');
              
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'totalpaid', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'tax23','title' => 'tax23','dbfield' => 'payabletax23', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trnotes',  'width' => 250)); 
    
            
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/apEmployeeCommissionPayment'));
        
        $this->includeClassDependencies(array(  
                  'AP.class.php',
                  'APEmployeeCommission.class.php',
                  'Downpayment.class.php'  ,
                  'SupplierDownpayment.class.php' ,
                  'GeneralJournal.class.php' ,
                  'COALink.class.php',
                  'APPayableTax23.class.php',
                  'Service.class.php',
                  'AREmployee.class.php',
                  'ARPayment.class.php',
                  'AREmployeePayment.class.php'
        ));  

        
        $this->overwriteConfig(); 
	}
	
    function getQuery(){
		if(PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking']){ 
			$sql = '
				SELECT '.$this->tableName.'.* ,
				   '.$this->tableEmployee.'.name as employeename,
				   '.$this->tableWarehouse.'.name as warehousename,
				   '.$this->tableWorkOrder.'.code as refcode,
				   '.$this->tableStatus.'.status as statusname
				FROM 
					'.$this->tableName.' 
						left join '.$this->tableWorkOrder.' on  '.$this->tableName.'.refkey = '.$this->tableWorkOrder.'.pkey
						left join '.$this->tableEmployee.' on  '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey , 
					'.$this->tableStatus.',  
					'.$this->tableWarehouse.'
				WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
			';
		}else{
			$sql = '
				SELECT '.$this->tableName.'.* ,
				   '.$this->tableEmployee.'.name as employeename,
				   '.$this->tableWarehouse.'.name as warehousename, 
				   '.$this->tableStatus.'.status as statusname
				FROM 
					'.$this->tableName.'  
						left join '.$this->tableEmployee.' on  '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey , 
					'.$this->tableStatus.',  
					'.$this->tableWarehouse.'
				WHERE '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
					  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey  
			';
		}
		 
		
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
				 

                $totalAREmployee = 0;
                $arEmployeeAmount = $arrParam['arEmployeeAmount'];
                $arEmployeeKey = $arrParam['selAREmployee'];

                for($i=0;$i<count($arEmployeeAmount);$i++){
					$totalAREmployee += $this->unFormatNumber($arEmployeeAmount[$i]);
				}
				 
				$grandtotal = $amount - $pph - $totalDowpayment - $totalAREmployee + $totalCost ;
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
                $reCountResult['totalAREmployee'] = $totalAREmployee;
				$reCountResult['grandtotal'] = $grandtotal;
				$reCountResult['balance'] = $balance;
                $reCountResult['totalCost'] = $totalCost;
               
				return $reCountResult;
               
				
	}
 
	
	function validateForm($arr,$pkey = ''){
        
		$APObj = $this->getAPObj();
        $downpayment = new SupplierDownpayment();
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
        
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$employeekey = $arr['hidEmployeeKey'];  
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
        $arrAPEmployee = array_column($rsAP, 'employeekey', 'pkey');
        $arrAPRefkey2 = array_column($rsAP, 'refkey2', 'pkey');
        $arrDate = array_column($rsAP, 'trdate', 'pkey');
         
        //khusus trucking
        $refCode = '';
        if(!empty($refkey)) {
            $rsWO =  $truckingServiceWorkOrder->getDataRowById($refkey);
            $refCode = $rsWO[0]['code'];
        } 
        
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
           
        $supplierDownpayment = new SupplierDownpayment();
        $rsDownpayment = $this->getDownpaymentDetail($rsHeader[0]['pkey'],'',false);
        for($i=0;$i<count($rsDownpayment); $i++){  
           $supplierDownpayment->updateOutstanding($rsDownpayment[$i]['downpaymentkey']); 
        }
        
        // retrieve latest status
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
        if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3); 
    }
    
	  
	function validateConfirm($rsHeader){
		
		$id = $rsHeader[0]['pkey'];
        $employeekey =  $rsHeader[0]['employeekey'];
        
		$coaLink = new COALink();
        $warehouse = new Warehouse();  
        $arEmployee = new AREmployee();
        $ap = $this->getAPObj();
         
        $rsPayment = $this->getPaymentMethodDetail($id); 
        //$rsDownpayment = $this->getDownpaymentDetail($id,'',false);
        $rsAREmployeeDetail = $this->getDetailAREmployee($id);
        
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
 
        // tidak ad DP di komisi karyawan
        //for($i=0;$i<count($rsDownpayment);$i++) {   
        //    
        //    // validasi DP masi available gk 
        //    if($rsDownpayment[$i]['downpaymentstatuskey'] <> 2){ 
        //        $this->addErrorLog(false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][9]);
        //    }else{
        //        //if($employeekey <> $rsDownpayment[$i]['downpaymentsupplierkey'])
        //            //$this->addErrorLog(false,$rsDownpayment[$i]['refcode']. '. ' . $this->errorMsg['downpayment'][7]); 
//
        //        // validasi nilai DP masi mencukupi gk 
        //        if ($rsDownpayment[$i]['amount'] > $rsDownpayment[$i]['downpaymentoutstanding'] )
        //            $this->addErrorLog(false,$arrDownpaymentCode[$i]. '. ' . $this->errorMsg['downpayment'][8].' ('.$this->lang['outstanding']. ': ' .$this->formatNumber($rsDownpayment[$i]['downpaymentoutstanding']['outstanding']).')');  
        //    }
        //        
        //}
       
        // cek status AR Employee
        $arrARKeys = array_column($rsAREmployeeDetail, 'aremployeekey');
        $rsAREmployee = $arEmployee->searchData('','',true, ' and ' . $arEmployee->tableName.'.pkey in ('.$this->oDbCon->paramString($arrARKeys,',').')  and ' . $arEmployee->tableName.'.statuskey in (1,2) ');
        
        $rsAREmployeeCols = array_column($rsAREmployee,null, 'pkey'); // reiindex berdasarkan pkey AR
        
        
        for($j=0; $j<count($rsAREmployeeDetail); $j++) {
            // kalo gk nemu AR nya (bisa sudah batal atau lunas)
            if(!isset($rsAREmployeeCols[$rsAREmployeeDetail[$j]['aremployeekey']])){
                $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'. </strong>' . $this->errorMsg[201] .'<br>'. $this->errorMsg['arEmployee'][7]);
            } else {
                $rsAREmployeeCol = $rsAREmployeeCols[$rsAREmployeeDetail[$j]['aremployeekey']];
                
                if($rsAREmployeeDetail[$j]['amount'] > $rsAREmployeeCol['outstanding']){
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'. </strong>' . $this->errorMsg[201] . '<br> <strong>'.$rsAREmployeeCol['code'].'. </strong>' . $this->errorMsg['arEmployee'][9]);  
                }
                if($rsHeader[0]['employeekey'] <> $rsAREmployeeCol['customerkey']) {
                    $this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'. </strong>' . $this->errorMsg[201] . '<br> <strong>'.$rsAREmployeeCol['code'].'. </strong>' . $this->errorMsg['arEmployee'][8]);
                }
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
        $id = $rsHeader[0]['pkey'];
         
        $coaLink = new COALink(); 
		$warehouse = new Warehouse();  
		$employee = $this->getBussinessPartnerObj();
		//$cashMovement = new CashMovement();  
        
		$rsEmployee = $employee->getDataRowById($rsHeader[0]['employeekey']);
		$notecash = $rsHeader[0]['code'].'. Kas Keluar untuk pembayaran hutang dari '.$rsEmployee[0]['name'];
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

        $this->addAREmployeePayment($rsHeader);
		
		
		//update jurnal umum 
        $this->updateGL($rsHeader);
        
//        if ($rsHeader[0]['payabletax23'] != 0) 
//            $this->updateAPPrepaid($rsHeader,$rsDetail); 
	} 
    
    function updateAPPrepaid($rsHeader,$rsDetail){
        
            $apPayableTax23 = $this->getPayableTaxObj();  
            $ap = $this->getAPObj();
            $warehouse = new Warehouse();
            $warehousekey = $warehouse->getDefaultData();
            $truckingServiceWorkOrder = new TruckingServiceWorkOrder();
            
            for ($i=0;$i<count($rsDetail);$i++){ 
                
                if ($rsDetail[$i]['taxamount'] == 0)
                    continue;
                    
                $arrParam = array();
                 
                $rsAP = $ap->getDataRowById($rsDetail[$i]['apkey']); 
                   
                // kalo add manual, gk ad obj
                $rsPO = array();
                if (!empty($rsAP[0]['reftabletype'])){
                        // cek tipe AP   
                        $type = $this->getTableNameAndObjById($rsAP[0]['reftabletype']); 
                        switch($type['tableName']){
                            case $truckingServiceWorkOrder->tableCost: $purchaseObj = $truckingServiceWorkOrder;
                                                                        break;
                            default : 
                                    $purchaseObj = $type['obj'];  
                        }
                    
                        $rsPO = $purchaseObj->getDataRowById($rsAP[0]['refkey']);
                }
                
           
                
                $rsAPKey =  $ap->getTableKeyAndObj($this->tableName);                  
                $arrParam['code'] = 'xxxxxx';
                $arrParam['hidEmployeeKey'] = $rsHeader[0]['employee']; 
                $arrParam['hidRefKey'] = $rsDetail[$i]['pkey']; //$rsPO[0]['pkey']; // kepake gk ??? harusnya ambil detailkey dr payment
                $arrParam['hidRefHeaderKey'] = $rsHeader[0]['pkey'];
                $arrParam['hidRefCode'] =  (!empty($rsPO)) ? $rsPO[0]['code'] : '';
                $arrParam['hidRefDate'] =  (!empty($rsPO)) ? $this->formatDBDate($rsPO[0]['trdate'],'d / m / Y') : DEFAULT_EMPTY_DATE;  
                $arrParam['hidRefTable'] = $rsAPKey['key'];
                $arrParam['amount'] = $rsDetail[$i]['taxamount'];
                $arrParam['trDesc'] = '';
                $arrParam['trDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['dueDate'] =  $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');  
                $arrParam['createdBy'] = 0; 
                $arrParam['islinked'] = 1;
                $arrParam['selAPType'] = 1;
                $arrParam['overwriteGL'] = 1;
                $arrParam['selWarehouse'] = $warehousekey;
                

                $returnVal = $apPayableTax23->addData($arrParam,false);  
 
            }  
    }
    
    
    function validateCancel($rsHeader, $autoChangeStatus = false){ 
         
        $id = $rsHeader[0]['pkey'];
        
        $ap = $this->getAPObj();
        $apPayableTax = $this->getPayableTaxObj();
   
        //cek ad Prepaid yg ad bukti potongnya blm 
        $rsAPKey = $ap->getTableKeyAndObj($this->tableName);                  
		/*$rsAP = $apPayableTax->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and reftabletype = '.$rsAPKey['key'].' and ('.$apPayableTax->tableName.'.statuskey in (2,3) )');
     
		if(!empty($rsAP)) {
            $arrAP = array_column($rsAP,'code');
			$this->addErrorLog( false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' Bukti bayar sudah diinput.<br>' . implode(', ', $arrAP ).'.');
        }*/
         
		    
	 } 
	 
 
	function cancelTrans($rsHeader,$copy){ 

        $id = $rsHeader[0]['pkey'];
		//$cashMovement = new CashMovement();    
		//$cashMovement->cancelMovement($id,$this->tableName);
        //$this->deleteAPPrepaidTax($id); 
		
        $this->cancelAREmployeePayment($rsHeader);
		if ($copy)
			$this->copyDataOnCancel($id);	  
		  
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	}
	
	 
	function updateGL($rs){
        if (!USE_GL) return;
        
		$warehouse = new Warehouse();
        $coaLink = new COALink();
        $generalJournal = new GeneralJournal();
        $employee = new Employee();
        $cost = new Service(TRUCKING_SERVICE,1);
		
        $warehousekey = $rs[0]['warehousekey'];
        
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName); 
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y'); 
		$arr['createdBy'] = 0;  
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
         
        //ar employee
        $rsAREmployeeDetail = $this->getDetailAREmployee($rs[0]['pkey']);
        for($i=0; $i<count($rsAREmployeeDetail); $i++) {
            $rsCOA = $coaLink->getCOALink('employeear', $warehouse->tableName,$warehousekey); 
            $temp++;
            $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
            $arr['debit'][$temp] = 0; 
            $arr['credit'][$temp] = $rsAREmployeeDetail[$i]['amount']; 
        }

        $rsCOAOperationalCost = $coaLink->getCOALink ('operationalcost', $warehouse->tableName, $warehousekey); 
		$rsCost = $this->getCostDetail($rs[0]['pkey']);  
		
		for($i=0;$i<count($rsCost); $i++){   
             //$rsItem = $cost->getDataRowById($rsCost[$i]['costkey']);  
             $coakey = $rsCost[$i]['coakey'] ; //(!empty($rsCost[0]['costcoakey'])) ? $rsItem[0]['costcoakey'] : $rsCOAOperationalCost[0]['coakey']; 
 
             $temp++;
             $arr['hidCOAKey'][$temp] = $coakey ;
             $arr['debit'][$temp] = $rsCost[$i]['amount']; 
             $arr['credit'][$temp] = 0;  
        }
        
         $rsCOA = $coaLink->getCOALink ('payabletax23', $warehouse->tableName,$warehousekey,0); 
         $temp++;
         $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
         $arr['debit'][$temp] = 0; 
         $arr['credit'][$temp] = $rs[0]['payabletax23'];  
 
		
        $temp++; 
        $rsCOA = $coaLink->getCOALink ('otherrevenue', $warehouse->tableName,$warehousekey, 0); 
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['totaldiscount'];  
		 
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
		$arr['hidCOAKey'][$temp] =  $employee->getAPCommissionCOAKey($rs[0]['employeekey'],$warehousekey);
		$arr['debit'][$temp] = $rs[0]['totalpaid']; 
		$arr['credit'][$temp] = 0;
  
        
		$arrayToJs = $generalJournal->addData($arr);
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);
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
          
        $apPayableTax23 = $this->getPayableTaxObj(); 
        $rsAP = $apPayableTax23->searchData('','',true,' and refheaderkey = '.$this->oDbCon->paramString($id).' and '.$apPayableTax23->tableName.'.statuskey = 1');
        //and reftabletype = '.$this->oDbCon->paramString($rsAR[0]['reftabletype']).' 
          
        for($i=0;$i<count($rsAP);$i++) { 
            $arrayToJs = $apPayableTax23->changeStatus($rsAP[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }  
          
      }
    
    function getAPObj(){
        return new APEmployeeCommission();
    }
    
    function getBussinessPartnerObj(){
        return new Employee();    
    }
    
    function getPayableTaxObj(){
        return new APPayableTax23();
    }

  function addAREmployeePayment($rsHeader)
    {
        $arEmployee = new AREmployee();
        $arEmployeePayment = new AREmployeePayment();


        $id = $rsHeader[0]['pkey'];
        $rsDetail = $this->getDetailById($id);

        if($rsHeader[0]['totalaremployee'] <= 0) return;

        $rsAREmployeeDetail = $this->getDetailAREmployee($id);

        $arrParam = array();
        $arrParam['code'] = 'xxxxxx';
        $arrParam['trDate'] = $this->formatDBDate($rsHeader[0]['trdate'],'d / m / Y');
        $arrParam['selWarehouseKey'] = $rsHeader[0]['warehousekey'];
        $arrParam['hidEmployeeKey'] = $rsHeader[0]['employeekey'];
        $arrParam['selCurrency'] = CURRENCY['idr'];
        $arrParam['currencyRate'] = 1;
        $arrParam['islinked'] = 1;
        $arrParam['overwriteGL'] = 1;
        $arrParam['trDesc'] = $rsHeader[0]['trnotes'];
        $arrParam['refAPEmployeeCommissionKey'] = $id;
        $arrParam['refAPEmployeeCommissionDetailKey'] = implode(',', array_column($rsDetail, 'pkey'));
    
        $arrParam['chkDatePeriod'] = $rsHeader[0]['usedateperiod'];
        $arrParam['trStartDate'] = $this->formatDBDate($rsHeader[0]['startdateperiod'],'d / m / Y');
        $arrParam['trEndDate'] = $this->formatDBDate($rsHeader[0]['enddateperiod'],'d / m / Y');

        $arrParam['hidDetailKey'] = array();
        $arrParam['hidARKey'] = array();
        $arrParam['outstanding'] = array();
        $arrParam['amount'] = array();
        $arrParam['chkPick'] = array();

        $arEmployeeKeys = array_column($rsAREmployeeDetail, 'aremployeekey');
        $rsAROutstanding = $arEmployee->searchDataRow(array(
                                        $arEmployee->tableName.'.pkey',
                                        $arEmployee->tableName.'.code',
                                        $arEmployee->tableName.'.outstanding',
                                        $arEmployee->tableName.'.amount'
                                        ), ' and ' . $arEmployee->tableName.'.statuskey in (1,2) 
                                            and '. $arEmployee->tableName.'.pkey in ('.$this->oDbCon->paramString($arEmployeeKeys,',').')  
                                    ');

        $rsAROutstanding = array_column($rsAROutstanding, 'outstanding', 'pkey');
        
        for($i=0; $i<count($rsAREmployeeDetail); $i++) {
            array_push($arrParam['hidDetailKey'], 0);
            array_push($arrParam['hidARKey'], $rsAREmployeeDetail[$i]['aremployeekey']);
            array_push($arrParam['outstanding'], $rsAROutstanding[$rsAPEmployeeDetail[$i]['aremployeekey']]);
            array_push($arrParam['amount'], $rsAREmployeeDetail[$i]['amount']);
            array_push($arrParam['chkPick'], 1);
        }

        $arrParam['hidSaveAndProceed'] = 1;
        
        $arrayToJs = $arEmployeePayment->addData($arrParam); 
        
        if (!$arrayToJs[0]['valid'])
            $this->addErrorLog(false, '<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message'], true); 

    }


    function cancelAREmployeePayment($rsHeader)
    {
        $id = $rsHeader[0]['pkey'];

        $arEmployeePayment = new AREmployeePayment();

        $rsAREmployeePayment = $arEmployeePayment->searchDataRow( array(  $arEmployeePayment->tableName.'.pkey', $arEmployeePayment->tableName.'.code'  ) , 
                                ' and '.$arEmployeePayment->tableName.'.refapemployeecommissionkey = '.$this->oDbCon->paramString($id)); 
		
        for($i=0;$i<count($rsAREmployeePayment);$i++) {
            $arrayToJs = $arEmployeePayment->changeStatus($rsAREmployeePayment[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);  
        }
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
        $arrParam['totalAREmployee'] = $reCountResult['totalAREmployee'];
       
        
        return $arrParam;
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

    function getDetailAREmployee($pkey)
    {
        
        $sql = '
            select
                '.$this->tableAREmployeeDetail.'.*,
                '.$this->tableAREmployee.'.code as aremployeecode,
                '.$this->tableAREmployee.'.trdate as aremployeedate,
                '.$this->tableAREmployee.'.outstanding,
                '.$this->tableAREmployee.'.amount as aremployeeamount,
                '.$this->tableAREmployee.'.trdesc,
                '.$this->tableAREmployee.'.statuskey as aremployeestatuskey
            from
                '.$this->tableAREmployeeDetail.', 
                '.$this->tableAREmployee.'
            where 
                '.$this->tableAREmployeeDetail.'.aremployeekey = '.$this->tableAREmployee.'.pkey and
                '.$this->tableAREmployeeDetail.'.refkey = '.$this->oDbCon->paramString($pkey).'
                order by  pkey asc
        ';
 
        $rs = $this->oDbCon->doQuery($sql);

        return $rs;

    }
    
    function getDetailWithRelatedInformation($pkey,$criteria='',$order=''){ 

        // sementara baru join ke table SPK
        // baru kepake utk TWJ
        $spkTableKey =  $this->getTableKeyAndObj($this->tableWorkOrder,array('key'))['key'];     
        $APObj = $this->getAPObj();

        $sql = 'select
                '.$this->tableNameDetail .'.*,
                '.$this->tableWorkOrder . '.pkey as workorderkey,
                '.$this->tableWorkOrder . '.trdate as workorderdate,
                '.$this->tableWorkOrder . '.routefrom,
                '.$this->tableWorkOrder . '.routeto,
                '.$this->tableCar . '.policenumber,
                '.$this->tableItemService . '.name as containername 
              from
                '.$this->tableNameDetail .'
                    left join ' . $APObj->tableName . ' on ' . $this->tableNameDetail . '.apkey = ' . $APObj->tableName . '.pkey 
                    left join ' . $this->tableWorkOrder . ' on ' . $APObj->tableName . '.refkey = ' . $this->tableWorkOrder . '.pkey  and
                                            ' . $APObj->tableName . '.reftabletype = '.$this->oDbCon->paramString($spkTableKey).' 
                    left join '.$this->tableCar.' on  ' . $this->tableWorkOrder . '.carkey =  '.$this->tableCar.'.pkey
                    left join '.$this->tableItemService.' on  ' . $this->tableWorkOrder . '.itemkey =  '.$this->tableItemService.'.pkey
              where 
                ' . $this->tableNameDetail . '.refkey in ('.$this->oDbCon->paramString($pkey,',') . ') ';

        $sql .=  ' ' .$criteria; 
        $sql .=  ' ' .$order;

		return $this->oDbCon->doQuery($sql);
	
   }
    
     
}

?>