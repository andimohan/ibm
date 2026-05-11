<?php

class AREmployee extends AR{
  
   function __construct(){
		
		parent::__construct();
		 
		$this->tableName = 'ar_employee';    
		$this->tableCustomer = 'employee';  
		$this->tableRefCustomer = 'customer';  // gk bisa pake customerkey, karena di AR Employee customerkey turunan dr AR (harusnya employeekey)
		//$this->tableRefJobOrder = 'trucking_service_order_header';   
		$this->tableCashBankRealization = 'cash_bank_realization_header';  
        $this->tableType = 'ar_employee_type';     
        $this->tablePayment = 'ar_employee_voucher_detail'; // harusnya tidak akan pernah kepake
		
		$this->securityObject = 'AREmployee';
        
        $this->arrPaymentDetail = array();
        $this->arrPaymentDetail['pkey'] = array('hidDetailPaymentKey');
        $this->arrPaymentDetail['refkey'] = array('pkey', 'ref');
        $this->arrPaymentDetail['amount'] = array('paymentMethodValue', array('datatype' => 'number', 'mandatory' => true));
        $this->arrPaymentDetail['paymentkey'] = array('selPaymentMethod'); // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja
        $this->arrPaymentDetail['cashbankvoucherkey'] = array('selVoucher');  // gk boleh mandatory, karena kadang pake payment kadang pake voucher, validasi di add saja

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrPaymentDetail, 'tableName' => $this->tablePayment));

        // overwrite dari AR karena ad detail voucher
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['agingdate'] = array('trAgingDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['saleskey'] = array('hidSalesKey');
        $this->arrData['refheaderkey'] = array('hidRefHeaderKey');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['refcode'] = array('hidRefCode');
        $this->arrData['refcode2'] = array('hidRefCode2');
        $this->arrData['refdate'] = array('hidRefDate','date');
        $this->arrData['reftabletype'] = array('hidRefTable');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['outstanding'] = array('amount','number');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['duedate'] = array('dueDate','date');
        $this->arrData['duedays'] = array('dueDays','number');
        $this->arrData['artype'] = array('selARType');
        $this->arrData['islinked'] = array('islinked');
        $this->arrData['overwriteGL'] = array('overwriteGL'); 
        $this->arrData['paymentmethodkey'] = array('selPaymentMethod'); 
        $this->arrData['currencykey'] = array('selCurrency'); 
        $this->arrData['rate'] = array('currencyRate','number'); 
        $this->arrData['amountidr'] = array('amountIDR','number');
        $this->arrData['refcustomerkey'] = array('hidRefCustomerKey'); 
        $this->arrData['refcashoutkey'] = array('hidRefCashOutKey'); 
        $this->arrData['refwokey'] = array('hidRefWOKey'); 
        $this->arrData['refsokey'] = array('hidRefSOKey'); 
        $this->arrData['tax23value'] = array('tax23value');  
        $this->arrData['tax23outstanding'] = array('tax23outstanding');  
        $this->arrData['vanumber'] = array('vaNumber');  
        $this->arrData['totalvoucher'] = array('totalPayment', 'number'); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'duedate','dbfield' => 'duedate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 100 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'jocode','title' => 'JOCode','dbfield' => 'reftranscode2', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename', 'default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding', 'default'=>true, 'width' => 100,  'align' =>'right',  'format' => 'integer' ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc',  'width' => 250)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'artype','title' => 'transactionType','dbfield' => 'artypename',  'width' => 120 ));
               
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/arEmployee'));
        
        $this->includeClassDependencies(array( 
            'AR.class.php',
            'ARPayment.class.php',
            'TruckingCostCashOut.class.php',
            'AREmployeePayment.class.php',
            'CashBank.class.php'
        ));
       
        $this->overwriteConfig();
	}  
 	
    function getQuery(){
	   
        // kalo dr realisasi
        $rsRrealizationKey = $this->getTableKeyAndObj($this->tableCashBankRealization, array('key'));
        
		$sql =  '
				select
					'.$this->tableName. '.*,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableCustomer.'.name as employeename, 
                    '.$this->tableCashBankRealization.'.refcode as reftranscode,
                    '.$this->tableCashBankRealization.'.refcode2 as reftranscode2,
                    '.$this->tableCashBankRealization.'.refcode3 as reftranscode3,
					'.$this->tableRefCustomer.'.name as customername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableType .'.name as artypename
				from 
					'.$this->tableName . '
                            left join '.$this->tableCashBankRealization.' on 
                                '.$this->tableName . '.reftabletype = '.$this->oDbCon->paramString($rsRrealizationKey['key']).' and
                                '.$this->tableCashBankRealization.'.pkey ='.$this->tableName . '.refheaderkey 
                            left join '.$this->tableRefCustomer.' on  '.$this->tableCashBankRealization . '.customerkey =  '.$this->tableRefCustomer . '.pkey
                            left join ' .  $this->tableType .' on  '.$this->tableName.'.artype = ' . $this->tableType .'.pkey, 
                    '.$this->tableStatus.' ,
                    '.$this->tableCustomer.' ,
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
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
		
        
                
      if ($this->isActiveModule('CashBank')) {
        if (ADV_FINANCE && TEST_VOUCHER){
            
            $cashBank = new CashBank();
             
            // untuk load voucher yg sudah pernah digunakan, karean AR Employee langsung update
            $rsPayment = $this->getPaymentVoucherDetail($pkey);  
            $rsPayment = array_column($rsPayment,null,'cashbankvoucherkey');
            
//            $voucherAmount = $this->unFormatNumber($arr['totalPayment']); // mending loop hitung ulang
            
            $arrVoucher = $arr['selVoucher']; 
            $arrPaymentValue = $arr['paymentMethodValue'];

            
            $rsCashBank = $cashBank->searchData('','', true, ' and ' . $cashBank->tableName.'.pkey in ('. $this->oDbCon->paramString($arrVoucher,',') .') ');
            $rsCashBank = array_column($rsCashBank,null,'pkey');
            
            $voucherAmount = 0;
            for($i=0; $i<count($arrVoucher); $i++) {
                $cashBankVoucherKey = $arrVoucher[$i];
                $cashBankVoucher = $rsCashBank[$cashBankVoucherKey];
                $existingPaymentDetailVoucher = (isset($rsPayment[$cashBankVoucherKey])) ? $rsPayment[$cashBankVoucherKey] : array();
                
                $amountPaid = (!empty($existingPaymentDetailVoucher)) ? $existingPaymentDetailVoucher['amount'] : 0;
                
                $paymentValue = $this->unFormatNumber($arrPaymentValue[$i]);
                $voucherAmount += $paymentValue;
                
                // validasi voucher lebih besar tdk paymentnya dibanding outstanding 
                 
//                $this->setLog($cashBankVoucher['outstanding'].'+'.$amountPaid .'<'. $paymentValue,true); 
//                 $this->setLog($cashBankVoucher['employeekey'].'+'.$customerkey,true);
                
                
                // balikin dulu amount sebelum dipake
                $voucherOutstanding = $cashBankVoucher['outstanding']+$amountPaid;
                if($voucherOutstanding != 0)  
                    $cashBankVoucher['statuskey'] = TRANSACTION_STATUS['konfirmasi'];
          
                if ($cashBankVoucher['employeekey'] <> $customerkey && $cashBankVoucher['employeekey']<> 0)
                     $this->addErrorList($arrayToJs,false,'<b>'.$cashBankVoucher['code']. '</b>. ' . $this->errorMsg['cashBank'][3]); 
                else if ($voucherOutstanding < $paymentValue)
                    // cek kalo outstanding masih cukup
                     $this->addErrorList($arrayToJs,false,'<b>'.$cashBankVoucher['code']. '</b>. ' . $this->errorMsg['cashBank'][4]); 
                else if ($cashBankVoucher['statuskey']  <> TRANSACTION_STATUS['konfirmasi'])
                     $this->addErrorList($arrayToJs,false,'<b>'.$cashBankVoucher['code']. '</b>. ' . $this->errorMsg['cashBank'][5]); 

                
                
                // validasi curr harus IDR sementara, karena gk ad piutang currency lain
                if($cashBankVoucher['currencykey'] <> CURRENCY['idr']) {
                    //cek currency
                    $this->addErrorList($arrayToJs, false, '<strong>'. $cashBankVoucher['code'] .'. </strong>'. $this->errorMsg['arEmployee'][6]);
                }
            }
            
             
            if ($voucherAmount > $amount) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg[509]);
            } else if ($voucherAmount < $amount) {
                $this->addErrorList($arrayToJs, false, $this->errorMsg[502]);
            }

             
        }
      }
        
        
    /*    $customer = new Customer();
        if ($customer->willExceedCreditLimit($customerkey,$amount)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['creditlimit'][1]);
        }*/
		return $arrayToJs;
	 } 
		 
    
    function getPaymentObj(){
        return  new AREmployeePayment();
    }
    
    function getCustomerObj(){
        return  new Employee();
    }
    
    function validateCancel($id,$autoChangeStatus=false){
		// perlu cek validasi lg kalo ad payment yg sudah dikonfirmasi bagaimana ?
        // atau gk perlu selama statusnya tdk open 
          
		$arrayToJs = array(); 
		$rs  = $this->getDataRowById($id);
           
        if ( !$autoChangeStatus ) {
            if(!empty($rs[0]['islinked'])) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' . $this->errorMsg['ar'][7]);    
        } 
         
        // transaksi tetep tidak boleh dibatalkan jika sudah ad pembayaran (status AR <> open)  
        // meskipun transaksi manual atau transaksi dr sales order 
        if ( $rs[0]['statuskey'] <> 1) 
                $this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[201]);     
              
		return $arrayToJs;
	 } 	
    
 
    
     function getARType($additionalType = array(),$overwriteType = false){
         
        $typekey = array();
        if(!$overwriteType){    
            array_push($typekey, AR_EMPLOYEE_TYPE['personalLoan'], AR_EMPLOYEE_TYPE['cashBankRealization']); 
        }
         
        $typekey = array_merge($typekey, $additionalType);
        
        $sql = 'select * from '.$this->tableType.' where pkey in ('.$this->oDbCon->paramString($typekey,',').')  and statuskey = 1 ';
        $rs = $this->oDbCon->doQuery($sql);	 
        
        return $rs;
    }
    
    
        
    function normalizeParameter($arrParam, $trim = false){ 
        
        $arrParam['hidCustomerKey'] = $arrParam['hidEmployeeKey'];

        $rate = (isset($arrParam['currencyRate']) && !empty($arrParam['currencyRate'])) ? $this->unFormatNumber($arrParam['currencyRate']) : 1;
        $arrParam['amountIDR'] =  $arrParam['amount'] * $rate;
        
        $arrParam = parent::normalizeParameter($arrParam,true);  
         
        return $arrParam;
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

        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName, array('key'));
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
        $rsEmployee = $employee->getDataRowById($rs[0]['customerkey']);  
        $employeARCOAKey = $rsEmployee[0]['arcoakey'];
        $rsCOAEmployeeAR = $coaLink->getCOALink ('employeear', $warehouse->tableName, $warehousekey); 
        $employeARCOAKey = (empty($employeARCOAKey)) ?  $rsCOAEmployeeAR[0]['coakey'] : $employeARCOAKey; 
 
        $desc = $this->lang['employeeAR'] .' '.$rsEmployee[0]['name'].'. '.$rs[0]['code'];
		$arr['trDesc'] = $desc;
        
        $temp++; 
        $arr['hidCOAKey'][$temp] = $employeARCOAKey;
        $arr['debit'][$temp] = $rs[0]['amount']; 
        $arr['credit'][$temp] = 0;  
        $arr['trdescDetail'][$temp] = ''; 
        
        $rsCOA = $coaLink->getCOALink ('payment', $warehouse->tableName,$warehousekey,$rs[0]['paymentmethodkey']);  
        
        $temp++; 
        $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['amount'];  
        $arr['trdescDetail'][$temp] = $desc ;

		$arrayToJs = $generalJournal->addData($arr); 
          
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    }
	
	function afterAddDataOnCopy($pkey, $oldkey){   
		 
		$rsHeader = $this->getDataRowById($pkey);
        $arrParam = array();
        $arrParam['pkey'] = $rsHeader[0]['pkey'];
        $arrParam['oldRs'] = '';  
 
        $this->afterUpdateData($arrParam,INSERT_DATA);  
		
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
                    '.$this->tableRefCustomer.'.name as customername
				from 
					'.$this->tableName . '
                        left join '.$this->tableCashBankRealization.' on '.$this->tableName . '.reftabletype = '.$this->oDbCon->paramString($rsKey['key']).' and
                        '.$this->tableCashBankRealization.'.pkey ='.$this->tableName . '.refheaderkey
                        left join '.$this->tableRefCustomer.' on  '.$this->tableCashBankRealization . '.customerkey =  '.$this->tableRefCustomer . '.pkey,
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
    
    function getTotalOutstanding($employeekey, $currencykey = CURRENCY['idr'], $criteria = '')
    {

        $sql = 'select 
                    coalesce(sum(outstanding),0) as totaloutstanding,
                    ' . $this->tableCurrency . '.name as currencyname
                from 
                    ' . $this->tableName . '
                    left join ' . $this->tableCurrency . ' on ' . $this->tableName . '.currencykey = ' . $this->tableCurrency . '.pkey
                where
                    ' . $this->tableName . '.customerkey = ' . $this->oDbCon->paramString($employeekey) . ' and
                    ' . $this->tableName . '.currencykey = ' . $this->oDbCon->paramString($currencykey) . ' and
                    ' . $this->tableName . '.outstanding > 0 and ' . $this->tableName . '.statuskey in (1,2) 
                ';

        //if (!empty($refkey))
        //    $sql .= ' and refkey = ' . $this->oDbCon->paramString($refkey);

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }

    // pake search data aja
    function getAvailableAR($employeekey, $arrExcKey = array())
    {
        
        //$arrExcKey utk load ulang transaksi yang sdh diproses
        
        if(!is_array($employeekey))
            $employeekey = array($employeekey);
        
        $criteriaExc = '';
        if (!empty($arrExcKey)) 
            $criteriaExc  = ' or '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrExcKey,',').') ';
    
        $rs = $this->searchDataRow(array( $this->tableName.'.pkey', $this->tableName.'.code', $this->tableName.'.amount',$this->tableName.'.trdate',$this->tableName.'.outstanding',$this->tableName.'.trdesc' ),
                                  ' and '.$this->tableName.'.statuskey in (1,2) and '.$this->tableName.'.customerkey in ('. $this->oDbCon->paramString($employeekey,',') .') '. $criteriaExc);
         
            
        $totalRs = count($rs);
        for ($i = 0; $i < $totalRs; $i++) {
            $rs[$i]['value'] = $this->formatNumber($rs[$i]['outstanding']) . ' (' . $this->formatDBDate($rs[$i]['trdate'], 'd / m / Y') . ', ' . $rs[$i]['trdesc'] . ')';
        }

        return $rs;

    }
    
		
}
?>