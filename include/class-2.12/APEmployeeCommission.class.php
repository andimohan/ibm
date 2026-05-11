<?php
class APEmployeeCommission extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'ap_employee_commission';   
		$this->securityObject = 'APEmployeeCommission'; 
		$this->tableStatus = 'ar_status';
		$this->tableEmploye = 'employee'; 
        $this->tableType = 'ap_type';
        $this->tableWarehouse = 'warehouse';
        $this->tableCurrency = 'currency';
        $this->tableLocation = 'location';
        $this->tableWO = 'trucking_service_work_order'; 
        $this->tableSO = 'trucking_service_order_header'; 
        $this->tableItem = 'item'; 
        $this->tableConsignee = 'consignee';
        $this->isTransaction = true;
	 
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
        $this->arrData['refheaderkey'] = array('hidRefHeaderKey');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refcode'] = array('hidRefCode');
        $this->arrData['refkey2'] = array('hidRefKey2');
        $this->arrData['refcode2'] = array('hidRefCode2');
        $this->arrData['refdate'] = array('hidRefDate','date');
        $this->arrData['reftabletype'] = array('hidRefTable');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['outstanding'] = array('amount','number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['duedate'] = array('dueDate','date');
        $this->arrData['aptype'] = array('selAPType');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['currencykey'] = array('selCurrency'); 
        $this->arrData['arstatuskey'] = array('hidARStatusKey'); 
        $this->arrData['rate'] = array('rate','number'); 
          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refdate','title' => 'jobsDate','dbfield' => 'refdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'duedate','dbfield' => 'duedate', 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'refCode','title' => 'reference','dbfield' => 'refcode', 'default'=>true,   'width' => 120 ));
        
       if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) )
           array_push($this->arrDataListAvailableColumn, array('code' => 'refCode2','title' => 'reference','dbfield' => 'refcode2', 'width' => 120 ));
        
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'container','title' => 'container','dbfield' => 'container', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'consigneename','title' => 'consignee','dbfield' => 'consigneename', 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'locationname','title' => 'location','dbfield' => 'locationname', 'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'route','title' => 'route','dbfield' => 'route', 'width' => 150)); 
        
        $this->includeClassDependencies(array( 
            'APEmployeeCommissionPayment.class.php',  
            'GeneralJournal.class.php'
        ));  

        $this->overwriteConfig(); 
        
	}
		
   function getQuery(){
	   
	   // sementara pecah 2 saja dulu
	   if(PLAN_TYPE['categorykey'] == COMPANY_TYPE['trucking']){ 
		   $sql = '
				select
					'.$this->tableName. '.*,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableEmployee.'.name as employeename,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename,  
                    
                    IF(
                        ('.$this->tableWO.'.containernumber IS NOT NULL AND '.$this->tableWO.'.containernumber != \'\')
                        AND ('.$this->tableWO.'.container2number IS NOT NULL AND '.$this->tableWO.'.container2number != \'\'),
                        CONCAT_WS(\', \', '.$this->tableWO.'.containernumber, '.$this->tableWO.'.container2number),
                        REPLACE(CONCAT_WS(\', \', '.$this->tableWO.'.containernumber, '.$this->tableWO.'.container2number), \',\', \'\')
                    ) container,
     
					'.$this->tableConsignee.'.name as consigneename, 
					'.$this->tableCustomer.'.name as customername, 
					'.$this->tableLocation.'.name as locationname, 
                    concat_ws(\' - \','.$this->tableWO.'.routefrom,'.$this->tableWO.'.routeto) as route ,
					'.$this->tableCurrency.'.name as currencyname ,
					'.$this->tableItem.'.name as servicename 
				from 
					'.$this->tableName . ' 
                        left join ' . $this->tableWO .' on  '.$this->tableName.'.refkey = ' . $this->tableWO .'.pkey
                        left join ' . $this->tableSO .' on  '.$this->tableWO.'.refkey = ' . $this->tableSO .'.pkey
                        left join ' . $this->tableConsignee .' on  '.$this->tableSO.'.consigneekey = ' . $this->tableConsignee .'.pkey
                        left join ' . $this->tableLocation .' on  '.$this->tableWO.'.locationkey = ' . $this->tableLocation .'.pkey
                        left join ' . $this->tableCustomer .' on  '.$this->tableSO.'.customerkey = ' . $this->tableCustomer .'.pkey
                        left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey 
                        left join ' . $this->tableItem .' on  '.$this->tableWO.'.itemkey = ' . $this->tableItem .'.pkey,
                    '.$this->tableStatus.',
                    '.$this->tableEmployee.',
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey
		' ; 
	   }else{
		   // standart
		    $sql = '
				select
					'.$this->tableName. '.*,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableEmployee.'.name as employeename,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename,     
					'.$this->tableCurrency.'.name as currencyname,
                    ar_status_table.status as arstatusname
				from 
					'.$this->tableName . '    
                        left join ' . $this->tableStatus .' ar_status_table on  '.$this->tableName.'.arstatuskey = ar_status_table.pkey 
                        left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey,
                    '.$this->tableStatus.',
                    '.$this->tableEmployee.',
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey
		' ; 
	   }
		
	   $sql .= $this->criteria;
        
       return $sql;
	}
	    
    function afterDuplicateData($rsHeader){ 
        $arrParam = array();
        $arrParam['pkey'] = $rsHeader[0]['pkey'];
        $arrParam['oldRs'] = '';  
 
        $this->afterUpdateData($arrParam);   
    }
    
    
    function afterUpdateData($arrParam, $action){ 
        $generalJournal = new GeneralJournal();
        $employee = new Employee();
        
        //$rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
        $rs = $this->getDataRowById($arrParam['pkey']);
        $oldRs = $arrParam['oldRs']; 
        
        $arr1 =array();
        array_push($arr1,$rs[0]['aptype']); 
        array_push($arr1,$rs[0]['warehousekey']); 
        array_push($arr1,$rs[0]['employeekey']); 
        array_push($arr1,$rs[0]['amount']); 
        array_push($arr1,$rs[0]['trdate']); 
        $arr1 = md5(json_encode($arr1));
         
        $arr2 = array();
        if(!empty($oldRs)){ 
            array_push($arr2,$oldRs[0]['aptype']); 
            array_push($arr2,$oldRs[0]['warehousekey']); 
            array_push($arr2,$oldRs[0]['employeekey']); 
            array_push($arr2,$oldRs[0]['amount']); 
            array_push($arr2,$oldRs[0]['trdate']); 
        }
        $arr2 = md5(json_encode($arr2));
        
        $same = ($arr1 == $arr2) ? true : false;
	           
        // kalo blm ad jurnal, add
        if (empty($oldRs)){ 
            $this->updateGL($rs);
        }else{
            if (!$same){ 
                //kalo ud ad cek perlu add ulang atau tidak
                $this->cancelGLByRefkey($arrParam['pkey'],$this->tableName);
                $employee->updateAPCommissionOutstanding($oldRs[0]['employeekey']);
                
                $this->updateGL($rs);
            } 
        }    
            
        $employee->updateAPCommissionOutstanding($rs[0]['employeekey']);
    }
     
	
    function afterAddDataOnCopy($pkey, $oldkey){    
        $rs = $this->getDataRowById($pkey);     
        $employee = new Employee();
        $employee->updateAPCommissionOutstanding($rs[0]['employeekey']); 
		 
        $arrParam = array();
        $arrParam['pkey'] = $rs[0]['pkey'];
        $arrParam['oldRs'] = '';   
        $this->afterUpdateData($arrParam,INSERT_DATA);
    }
    
    function updateGL($rs){
        if (!USE_GL) return;
         
        if ($rs[0]['overwriteGL'] == 1)
            return;
         
        //kalo amount sama gk perlu cancel
        $this->cancelGLByRefkey($rs[0]['pkey'],$this->tableName); 
        
        $coaLink = new COALink(); 
        $warehouse = new Warehouse();  
        $generalJournal = new GeneralJournal();
        $employee = new Employee();
		
        $warehousekey = $rs[0]['warehousekey']; 
            
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['refCode'] = $rs[0]['code'];
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
		$temp = -1; 
		   
        switch ($rs[0]['aptype']){ 
              
            // purchase
            default : 
                    $rsCOA = $coaLink->getCOALink ('commissioncost', $warehouse->tableName, $warehousekey);   
                    $temp++;
                    $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                    $arr['debit'][$temp] = $rs[0]['amount']; 
                    $arr['credit'][$temp] = 0;
                
                    break;
          
        }
        
        
        //akun hutang 
        $temp++; 
        $arr['hidCOAKey'][$temp] = $employee->getAPCommissionCOAKey($rs[0]['employeekey'],$warehousekey);
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['amount'];  

        
		$arrayToJs = $generalJournal->addData($arr); 
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
    
	function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		 
		$employeekey = $arr['hidEmployeeKey']; 
		$amount = $this->unFormatNumber($arr['amount']);
		
         
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
		if (!is_numeric($amount) || $amount <= 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][2]);
		}
		
		  	
		return $arrayToJs;
	 } 
	 
	 function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
         
		$sql = 'select
					'.$this->tableName. '.pkey,     
                    concat('.$this->tableName.'.code ,  IFNULL(concat(\'-\','.$this->tableName. '.refcode), \'\') ) as value , 
                    '.$this->tableName. '.code as code , 
                    '.$this->tableName.'.refcode, 
                    '.$this->tableName.'.refcode2,
                    '.$this->tableName.'.refdate, 
                    '.$this->tableName. '.amount,  
                    '.$this->tableName. '.outstanding
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
	
		if(!empty($fieldname)){
			
			$sql .= ' and ' ;
			
			if($mustmatch)
				$sql .=  $fieldname .' = '. $this->oDbCon->paramString($searchkey);
			else
				$sql .=  '('.$fieldname .' like '. $this->oDbCon->paramString('%'.$searchkey.'%') .' || '. $this->tableName .'.refcode like '. $this->oDbCon->paramString('%'.$searchkey.'%').')';
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> ''){
			$sql .= ' ' .$orderCriteria;
	 
	 	}
			
		if($limit <> '')
			$sql .= ' ' .$limit;
		     
		return $this->oDbCon->doQuery($sql);	
	}
	
	function changeStatus($id,$status,$reason='',$copy=false,$autoChangeStatus=false, $dontValidate = false){
		
		$arrayToJs = array();
		  
		  try{ 
			     $rs = $this->getDataRowById($id);
              
                if(!$dontValidate){
                   switch ($status){
                               case 1 : $arrayToJs = $this->validateOpen($id);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 
                                case 2 : $arrayToJs = $this->validatePartial($id);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 
                                 case 3 : $arrayToJs = $this->validateClosed($id);
                                             if (!empty($arrayToJs)) 
                                                    return $arrayToJs;  
                                              break; 
                                case 4 : $arrayToJs = $this->validateCancel($id, $autoChangeStatus);
                                         if (!empty($arrayToJs)) 
                                                return $arrayToJs;  
                                          break; 

                    } 
                } 
		  
			
			if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
		 	
						 
			switch ($status){  
				case 4 : $this->cancelTrans($id,$copy);
                          $this->afterCancelTrans($id);
                          break;  
			}
            
            $sql = 'update '.$this->tableName.' set statuskey = '.$this->oDbCon->paramString($status).' where pkey = ' . $this->oDbCon->paramString($id);
			$this->oDbCon->execute($sql);
 
            $rsStatus = $this->getStatusById ($status); 
            $this->setTransactionLog($rsStatus[0]['pkey'],$id);	
               
            $this->afterStatusChanged($rs);
            
			$this->oDbCon->endTrans();
			$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   
		
	    } catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());
		}		
				 
 		return $arrayToJs; 
 	}
	 
	
	
	function delete($id, $forceDelete = false,$reason = ''){ 
		
		 $arrayToJs =  array();  
		 $arrayToJs = $this->changeStatus($id, 4);   // harus ad validasi kalo islinked, gk boleh dihapus
		 return $arrayToJs; 

	}
	
   function cancelTrans($id,$copy){   
        $rsHeader = $this->getDataRowById($id); 
        $paymentObj = $this->getPaymentObj();
       
	   
        $autoCancel = $this->loadSetting('autoCancelAPEmployeeCommissionPayment');
	   
        if($autoCancel == 1){
			$sql = 'select 
						'.$paymentObj->tableName.'.pkey
					from
						'.$paymentObj->tableName.','.$paymentObj->tableNameDetail .'
					where
						'.$paymentObj->tableName.'.pkey = '.$paymentObj->tableNameDetail.'.refkey and
						'.$paymentObj->tableName.'.statuskey = 1 and
						'.$paymentObj->tableNameDetail.'.apkey = '.$paymentObj->oDbCon->paramString($id).' 
					';

			$rs = $paymentObj->oDbCon->doQuery($sql);

			for($i=0;$i<count($rs);$i++) 
				$paymentObj->changeStatus($rs[$i]['pkey'],4,'',false,true);
		}
	   
       
		if ($copy)
			$this->copyDataOnCancel($id);	  
	  
        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);
	} 
    
    // ============= MAKE SURE USER CANNOT MANUAL UPDATE STATUS
	 function validateOpen($id){ 
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
	 } 	
    
    
	 function validatePartial($id){ 
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['ap'][3]);
     	return $arrayToJs;
	 } 	
    
    function validateClosed($id){ 
         
        $arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
		$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg[201]);
		return $arrayToJs;
      
	 } 	 
    // ============= MAKE SURE USER CANNOT MANUAL UPDATE STATUS
    
    
	function validateCancel($id,$autoChangeStatus=false){ 
         // perlu cek validasi lg kalo ad payment yg sudah dikonfirmasi bagaimana ?
        // atau gk perlu selama statusnya tdk open 
          
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
           
        if ( !$autoChangeStatus ) {
            if(!empty($rs[0]['islinked'])) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['ap'][4]);    
        } 
         
        // transaksi tetep tidak boleh dibatalkan jika sudah ad pembayaran (status AR <> open)  
        // meskipun transaksi manual atau transaksi dr sales order 
        if ( $rs[0]['statuskey'] <> 1) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[201]);     
              
		return $arrayToJs;
	 } 	
	 
    
    	
    function getAPOutstanding($employeekey){
        $sql = 'select coalesce(sum(outstanding*rate),0) as outstanding from ' . $this->tableName .' where employeekey = ' . $this->oDbCon->paramString($employeekey) .' and (statuskey = 1 or statuskey = 2)' ;
        $rs = $this->oDbCon->doQuery($sql);
        return $rs[0]['outstanding'];
    }
      
    function getPaymentObj(){
        return  new APEmployeeCommissionPayment();
    }
    
    function getEmployeeObj(){
        return  new Employee();
    }
     
    
    function getAPType(){
        
        $typekey = '4';
        if ( in_array(PLAN_TYPE['categorykey'], array(COMPANY_TYPE['trucking'],COMPANY_TYPE['forwarding'])) )
            $typekey = '3,4';
        
        $sql = 'select * from '.$this->tableType.' where pkey in ('.$typekey.')';
        $rs = $this->oDbCon->doQuery($sql);	
        
        return $rs;
    }
    

    
    function normalizeParameter($arrParam, $trim = false){
         
        $arrParam['selCurrency'] = (!empty($arrParam['selCurrency'])) ? $arrParam['selCurrency'] : CURRENCY['idr'];
         
        $arrParam = parent::normalizeParameter($arrParam,true);  
        //$arrParam['hidRefDate'] = (!empty($arrParam['hidRefDate'])) ? $arrParam['hidRefDate'] : DEFAULT_EMPTY_DATE; 
         
        // old rs
        $oldRs = $this->getDataRowById($arrParam['pkey']);
        $arrParam['oldRs'] = $oldRs;
        
        return $arrParam;
    }
    
     
	function updateAPCommissionOutstanding($apkey){
	    $apPaymentObj = $this->getPaymentObj(); 
		$rsAP = $this->getDataRowById($apkey);
		
        if($rsAP[0]['statuskey'] == 4) return;
        
		$sql = 'select 
						coalesce(sum('.$apPaymentObj->tableNameDetail.'.amount + '.$apPaymentObj->tableNameDetail.'.discount),0) as totalPaidAmount
				 from 
				 	' . $apPaymentObj->tableName.','.$apPaymentObj->tableNameDetail. '
				 where ' . $apPaymentObj->tableNameDetail.'.refkey = '.$apPaymentObj->tableName .'.pkey and 
				 	  ('.$apPaymentObj->tableName .'.statuskey = 2 or '.$apPaymentObj->tableName .'.statuskey = 3 )and
					  '.$apPaymentObj->tableNameDetail.'.apkey = '.$apPaymentObj->oDbCon->paramString($apkey).'
				'  ;
         
		$rsAmount =  $this->oDbCon->doQuery($sql); 
		$totalPaidAmount = $rsAmount[0]['totalPaidAmount'];    
	
	
		if ($totalPaidAmount >= $rsAP[0]['amount'])
			$statuskey = 3;
		elseif ($totalPaidAmount <= 0)
			$statuskey = 1;
		else
			$statuskey = 2;
			
	    $sql  = 'update '.$this->tableName.' set outstanding = amount - ' . $totalPaidAmount .' where statuskey <> 4 and pkey = ' .$apkey ;	 
	    $this->oDbCon->execute($sql);  
		
        if($rsAP[0]['statuskey'] <> $statuskey)
            $this->changeStatus($apkey,$statuskey, '', false, true,true);
        
	}
	
    
    
       /* function getInvoiceType($tableName){ 
        
        $purchaseOrder = new PurchaseOrder();
        $truckingServiceWorkOrder = new TruckingServiceWorkOrder();  
        $apPayment = new APPayment();
        $salesOrderCarService = new SalesOrderCarService();
        
        $arr = array();
        
        switch ($tableName){ 
            case $truckingServiceWorkOrder->tableName : $arr = array('key' => 2,  
                                                                   'obj' => $truckingServiceWorkOrder 
                                                                  );
                                                      break; 

  	        case $apPayment->tableName : $arr = array('key' => 3,  
                                                       'obj' => $apPayment 
                                                      );
                                                break; 
  	        case $salesOrderCarService->tableName : $arr = array('key' => 4,  
                                                                 'obj' => $salesOrderCarService 
                                                      );
                                                break; 
                
            default : $arr = array('key' => 1,  
                           'obj' => $purchaseOrder 
                          );
        }
        
        return $arr;
        
    }*/
     
     
}
 	
?>
