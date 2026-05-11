<?php

class APEmployee extends AP{
  
   function __construct(){
		 
		parent::__construct();
		 
		$this->tableName = 'ap_employee';
	    $this->tableType = 'ar_employee_type';
	   	$this->tableSupplier = 'employee';      
		$this->tableCustomer = 'customer';
		$this->tableRefJobOrder = 'trucking_service_order_header';   
		$this->tableCashBankRealization = 'cash_bank_realization_header';   
		
		$this->securityObject = 'APEmployee';
	   
	   	$this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'duedate','dbfield' => 'duedate', 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jocode','title' => 'JOCode','dbfield' => 'reftranscode2', 'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'aptype','title' => 'transactionType','dbfield' => 'aptypename',  'width' => 120 ));
	   
	   	$this->printMenu = array();
	   
	   	$this->includeClassDependencies(array( 
            'AP.class.php',
            'APPayment.class.php',
            'APEmployeePayment.class.php',
            'Employee.class.php',
			'PaymentMethod.class.php',
            'Warehouse.class.php'
        ));
	}  
  
    function getQuery(){
	    // sementara nama customer ambil dr JO
        
        // kalo dr realisasi
        $rsRrealizationKey = $this->getTableKeyAndObj($this->tableCashBankRealization, array('key'));
        
		$sql=  '
				select
					'.$this->tableName. '.*,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableSupplier.'.name as employeename,
                    '.$this->tableCashBankRealization.'.refcode as reftranscode,
                    '.$this->tableCashBankRealization.'.refcode2 as reftranscode2,
                    '.$this->tableCashBankRealization.'.refcode3 as reftranscode3,
					'.$this->tableCustomer.'.name as customername, 
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename ,
                    '.$this->tableType .'.name as aptypename 
				from 
					'.$this->tableName . '
                         left join '.$this->tableCashBankRealization.' on 
                                '.$this->tableName . '.reftabletype = '.$this->oDbCon->paramString($rsRrealizationKey['key']).' and
                                '.$this->tableCashBankRealization.'.pkey ='.$this->tableName . '.refheaderkey 
                            left join '.$this->tableCustomer.' on  '.$this->tableCashBankRealization . '.customerkey =  '.$this->tableCustomer . '.pkey
                            left join ' .  $this->tableType .' on  '.$this->tableName.'.aptype = ' . $this->tableType .'.pkey, 
                    '.$this->tableStatus.',
                    '.$this->tableSupplier.',
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey
		' .$this->criteria ; 
        
        return $sql;
	}  
    
    function validateForm($arr,$pkey = ''){
		   
        // gk bisa inherit dr parentnya parent
         
        $arrayToJs = array();
         
        if(!empty($pkey)){
            $latestModifiedOn = $arr['hidModifiedOn'];
            
            $rs = $this->getDataRowById($pkey);
            if ($rs[0]['modifiedon'] <> $latestModifiedOn)
			     $this->addErrorList($arrayToJs,false,$this->errorMsg[214]);
            
            // jika linked dr data lain, tdk boleh edit
            if (isset($rs[0]['islinked']) && $rs[0]['islinked'] == 1)
                 $this->addErrorList($arrayToJs,false,$this->errorMsg[900]);
        }
        
        if(isset($arr['code']) && !empty($arr['code'])){ 
            $code = $arr['code'];   
            $rs = $this->isValueExisted($pkey,'code',$code);	 
            if(empty($code)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['code'][1]);
            }else if(count($rs) <> 0){
                //$this->setLog($rs);
                $this->addErrorList($arrayToJs,false,$this->errorMsg['code'][2]);
            }
        }
		 
		$customerkey = $arr['hidEmployeeKey']; 
		$amount = $this->unFormatNumber($arr['amount']);
		 
        //validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
        
		if(empty($customerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['employee'][1]);
		}
		if (!is_numeric($amount) || $amount <= 0){  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][2]);
		}
		
 
		return $arrayToJs;
         
	 } 
    
    function getPaymentObj(){
        return  new APEmployeePayment();
    }
	
	function normalizeParameter($arrParam, $trim = false){  
        
        $arrParam['hidSupplierKey'] = $arrParam['hidEmployeeKey'];
        
        $arrParam = parent::normalizeParameter($arrParam,true);  
         
        return $arrParam;
    }
    
	function afterAddDataOnCopy($pkey, $oldkey){     
		$rsHeader = $this->getDataRowById($pkey);
        $arrParam = array();
        $arrParam['pkey'] = $rsHeader[0]['pkey'];
        $arrParam['oldRs'] = '';   
        $this->afterUpdateData($arrParam,INSERT_DATA);   
    }
    
	
    function updateGL($rs){
        if (!USE_GL) return;
         
        if ($rs[0]['overwriteGL'] == 1)
            return;
         
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
		$arr['createdBy'] = 0;
		$arr['refCode'] = $rs[0]['code'];
		$arr['selWarehouseKey'] = $rs[0]['warehousekey'];
		
		$temp = -1;   
        
        //akun piutang   
        $rsEmployee = $employee->getDataRowById($rs[0]['supplierkey']);   
        $employeAPCOAKey = $rsEmployee[0]['apcoakey'];
        $rsCOAEmployeeAP = $coaLink->getCOALink ('employeeap', $warehouse->tableName, $warehousekey); 
        $employeAPCOAKey = (empty($employeAPCOAKey)) ?  $rsCOAEmployeeAP[0]['coakey'] : $employeAPCOAKey; 
 
        $desc = $this->lang['employeeAP'] .' '.$rsEmployee[0]['name'].'. '.$rs[0]['code'];
		$arr['trDesc'] = $desc;
        
        $temp++; 
        $arr['hidCOAKey'][$temp] = $employeAPCOAKey;
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['amount'];  
        $arr['trdescDetail'][$temp] = ''; 
        
        $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rs[0]['paymentmethodkey']);  
        
        $temp++; 
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = $rs[0]['amount']; 
        $arr['credit'][$temp] = 0;  
        $arr['trdescDetail'][$temp] = $desc ;

		$arrayToJs = $generalJournal->addData($arr); 
          
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
    
    function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
		
         $rsKey = $this->getTableKeyAndObj($this->tableCashBankRealization, array('key'));
         
         $sql = 'select
					'.$this->tableName. '.pkey, 
                    concat('.$this->tableName.'.code ,  IFNULL(concat(\'-\','.$this->tableName. '.refcode), \'\') ) as value , 
                    '.$this->tableName. '.code as code , 
                    '.$this->tableName.'.refcode,
                    '.$this->tableName.'.refcode2,
                    '.$this->tableName.'.refkey,
                    '.$this->tableName.'.refdate, 
                    '.$this->tableName. '.amount,  
                    '.$this->tableName. '.outstanding,
                    '.$this->tableCashBankRealization.'.refcode as reftranscode,
                    '.$this->tableCashBankRealization.'.refcode2 as reftranscode2,
                    '.$this->tableCashBankRealization.'.refcode3 as reftranscode3,
                    '.$this->tableCustomer.'.name as customername
				from 
					'.$this->tableName . '
                        left join '.$this->tableCashBankRealization.' on '.$this->tableName . '.reftabletype = '.$this->oDbCon->paramString($rsKey['key']).' and
                        '.$this->tableCashBankRealization.'.pkey ='.$this->tableName . '.refheaderkey                        
                        left join '.$this->tableCustomer.' on  '.$this->tableCashBankRealization . '.customerkey =  '.$this->tableCustomer . '.pkey,
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

    
}
?>
