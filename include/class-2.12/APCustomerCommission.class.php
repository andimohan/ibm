<?php

class APCustomerCommission extends AP{
  
   function __construct(){
		 
		parent::__construct();
		 
		$this->tableName = 'ap_customer_commission'; 
		$this->tableCustomer = 'customer';
		$this->tableSalesOrder = 'sales_order_header';  
	   	$this->tableCurrency = 'currency'; 
	   	$this->tableType = 'ap_type'; 
	    $this->tableMembershipSubscription = 'membership_subscription';
        $this->isTransaction = true;

		$this->securityObject = 'APCustomerCommission'; 
       
	    $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
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
        $this->arrData['rate'] = array('rate','number'); 
       
       
	   	$this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'duedate','dbfield' => 'duedate', 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'referral','title' => 'referral','dbfield' => 'referralname','default'=>true,  'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true,  'width' => 150));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'socode','title' => 'soCode','dbfield' => 'reftranscode','default'=>true,  'width' => 100)); 
        //array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'currency','title' => 'curr','dbfield' => 'currencycode','default'=>true, 'width' => 60, 'align' => 'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'aptype','title' => 'transactionType','dbfield' => 'aptypename',  'width' => 120 ));
	   
	   	$this->printMenu = array();    
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/apCustomerCommission'));
	  
	   
	   	$this->includeClassDependencies(array( 
            'AP.class.php',
            'APPayment.class.php',
            'APCustomerCommissionPayment.class.php',
            'Customer.class.php',
			'PaymentMethod.class.php',
			'SalesOrder.class.php',
			'Currency.class.php', 
            'Warehouse.class.php'
        ));
	}  
  
    function getQuery(){
		// sementara join ke mmebership dulu
		
		$sql=  '
				select
					'.$this->tableName. '.*,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableCustomer.'.name as customername,
                    '.$this->tableMembershipSubscription.'.code as reftranscode,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename ,
                    '.$this->tableType .'.name as aptypename,
					referral.name as referralname,
                    '.$this->tableCurrency.'.code as currencycode
				from 
					'.$this->tableName . '
                    left join '.$this->tableType .' on  '.$this->tableName.'.aptype = ' . $this->tableType .'.pkey
                    left join '.$this->tableMembershipSubscription.' on  '.$this->tableName . '.refkey =  '.$this->tableMembershipSubscription . '.pkey
                    left join '.$this->tableCustomer.'  on  '.$this->tableCustomer.'.pkey =  '.$this->tableMembershipSubscription. '.customerkey 
                    left join '.$this->tableCustomer.' referral  on  referral.pkey =  '.$this->tableName . '.customerkey
                    left join '.$this->tableCurrency.' on  '.$this->tableName.'.currencykey =  '.$this->tableCurrency.'.pkey,
                    '.$this->tableStatus.', 
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey  
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
		 
		$customerkey = $arr['hidCustomerKey']; 
		$amount = $this->unFormatNumber($arr['amount']);
		 
        //validasi kalo status gk menunggu gk bisa edit 
		if (!empty($pkey)){
			$rs = $this->getDataRowById($pkey);
			if ($rs[0]['statuskey'] <> 1){
				$this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
			}
		}  
        
		if(empty($customerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		}
        
		if (!is_numeric($amount) || $amount <= 0){  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][2]);
		}
		 
		return $arrayToJs;
         
	 } 
    
    function getPaymentObj(){
        return  new APCustomerCommissionPayment();
    }
	
	function normalizeParameter($arrParam, $trim = false){   
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
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
        $customer = new Custpmer();
		
        $warehousekey = $rs[0]['warehousekey']; 
            
        $rsKey = $generalJournal->getTableKeyAndObj($this->tableName);
		$arr = array();
		$arr['pkey'] = $generalJournal->getNextKey($generalJournal->tableName);
		$arr['code'] = 'xxxxx';
		$arr['refkey'] = $rs[0]['pkey'];
		$arr['refTableType'] = $rsKey['key'];
		$arr['trDate'] =  $this->formatDBDate($rs[0]['trdate'],'d / m / Y');  
		$arr['refCode'] = $rs[0]['code'];
		
		$temp = -1; 
		   
        switch ($rs[0]['aptype']){ 
             
            // commission
            default : 
                    $rsCOA = $coaLink->getCOALink ('commissioncost', $warehouse->tableName, $warehousekey);   
                    $temp++;
                    $arr['hidCOAKey'][$temp] = $rsCOA[0]['coakey'];
                    $arr['debit'][$temp] = $rs[0]['amount']; 
                    $arr['credit'][$temp] = 0;
                
                    break;
          
        }
        
//        $coakey = $customer->getCommissionCOAKey($rs[0]['supplierkey'],$warehousekey);
        
        $temp++; 
        $arr['hidCOAKey'][$temp] = $coakey;
        $arr['debit'][$temp] = 0; 
        $arr['credit'][$temp] = $rs[0]['amount'];  

        
		$arrayToJs = $generalJournal->addData($arr); 
        
		if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rs[0]['code'] . '</strong>. '.$this->errorMsg[504].' '.$arrayToJs[0]['message']);    
 
    } 
  	function getAPObj(){
        return new APCustomerCommission();
    }
	 
	function updateAPCommissionOutstanding($apkey){
	    $apPaymentObj = $this->getPaymentObj(); 
		$rsAP = $this->getDataRowById($apkey);
        
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
	
	// harus dioverwrite agar gk nembak ke outstanding supplier dan TMS
    function afterStatusChanged($rsHeader){ 
		
	}
	
	
	function searchDataForAutoComplete($fieldname='',$searchkey='',$mustmatch=false,$searchCriteria='',$orderCriteria='', $limit=''){
         
		$sql = 'select
					'.$this->tableName. '.pkey,     
                    concat('.$this->tableName.'.code ,  IFNULL(concat(\'-\','.$this->tableName. '.refcode), \'\') ) as value , 
                    '.$this->tableName. '.code as code , 
                    '.$this->tableName.'.refcode, 
                    '.$this->tableName.'.duedate, 
                    '.$this->tableName.'.refcode2,
                    '.$this->tableName.'.refkey,
                    '.$this->tableName.'.refdate, 
                    '.$this->tableName. '.amount,  
                    '.$this->tableName. '.currencykey, 
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
		 
}
?>