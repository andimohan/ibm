<?php
class CustomerMembership extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'customer_membership';
        $this->tableMember = 'membership';
        $this->tableCustomer = 'customer';
        $this->tableVoucherTransaction = 'voucher_transaction';
		$this->tableStatus = 'transaction_status';  
		$this->securityObject = 'CustomerMembership'; 
	    $this->isTransaction = true; 
		
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
 //       $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['customerkey'] = array('hidCustomerKey');
	   	$this->arrData['activationdate'] = array('activationDate','date');
	   	$this->arrData['membershipkey'] = array('selMembership');
	    $this->arrData['trdesc'] = array('trDesc');
	    $this->arrData['referralkey'] = array('hidReferralKey');
	    $this->arrData['voucherkey'] = array('selVoucher');
	    $this->arrData['statuskey'] = array('selStatus'); 
	    $this->arrData['maxattendance'] = array('maxAttendance','number');
        $this->arrData['timelimit'] = array('timeLimit','number');
        $this->arrData['price'] = array('registrationCost','number');
        $this->arrData['discountvalue'] = array('discountValue','number');
        $this->arrData['total'] = array('balance','number');
        
                
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'activationdate','title' => 'activationDate','dbfield' => 'activationdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
        //array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'referral','title' => 'referral','dbfield' => 'referralname',  'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'membership','title' => 'membership','dbfield' => 'membershipname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'attendance','title' => 'attendance','dbfield' =>  'attendance','default'=>true, 'width' => 80, 'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'maxAttendance','title' => 'maxAttendance','dbfield' =>  'maxattendance','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'timeLimit','title' => 'timeLimit','dbfield' => 'timelimit','default'=>false, 'width' => 100, 'align' => 'right', 'format'=>'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'price','title' => 'price','dbfield' => 'price','default'=>false, 'width' => 100, 'align' => 'right', 'format'=>'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'discount','title' => 'discount','dbfield' => 'discountvalue','default'=>false, 'width' => 100, 'align' => 'right', 'format'=>'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'total','title' => 'total','dbfield' => 'total','default'=>false, 'width' => 100, 'align' => 'right', 'format'=>'number'));
		array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
	   	array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));

        //array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
	}
	   
    function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
                    '.$this->tableCustomer.'.name as customername,
                    referralcustomer.name as referralname,
                    '.$this->tableMember.'.name as membershipname, 
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.' left join 
                        '.$this->tableCustomer.' as referralcustomer on '.$this->tableName.'.referralkey = referralcustomer.pkey,
                    '.$this->tableCustomer.',
                    '.$this->tableMember.',
                    '.$this->tableStatus.'
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableName.'.membershipkey = '.$this->tableMember.'.pkey
 		' .$this->criteria ; 
		 
    }
    
	function validateForm($arr,$pkey = ''){
		   
        $voucherTransaction = new VoucherTransaction();
		$arrayToJs = parent::validateForm($arr,$pkey);

        $memberkey = $arr['selMembership'];
        $customerkey = $arr['hidCustomerKey'];  
        $voucherkey = $arr['selVoucher'];  
        $rsVoucher = $voucherTransaction->getDataRowById($voucherkey);
		
        if(empty($memberkey))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['membership'][1]);
        
		if(empty($customerkey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
		}

        if(!empty($rsVoucher) && $rsVoucher[0]['statuskey'] != 2)
            $this->addErrorList($arrayToJs,false,$this->errorMsg['voucher'][3]);
        
		return $arrayToJs;
	 }
    
    
    function confirmTrans($rsHeader){  
         
        $referallCustomerKey = $rsHeader[0]['customerkey'];
        $customerkey = $rsHeader[0]['referralkey'];
        
        $sql = 'update  '.$this->tableName.' set 
                    activationdate = curdate(),
                    expdate = curdate() + INTERVAL '.$rsHeader[0]['timelimit'].' MONTH
                where 
                    pkey = ' . $this->oDbCon->paramString($rsHeader[0]['pkey']);
 
        $this->oDbCon->execute($sql);  
        
        // update voucher
        //cek ad voucher yg berlaku gk
        
        if(!empty($customerkey)){
            $voucher = new Voucher();
            $rsVoucher = $voucher->getAvailableVoucher(VOUCHER_CATEGORY['registration']);
  
            if(!empty($rsVoucher)){
                // add voucher
                    $voucherTransaction = new VoucherTransaction();
                    $warehouse = new Warehouse();
                
                    $rsTableKey = $this->getTableKeyAndObj($this->tableName);  
                
                    $arr = array();

                    $arr['code'] = array('code'); 
                    $arr['trDate'] = date('d / m / Y');
                    $arr['selWarehouse'] = $warehouse->getDefaultData();
                    $arr['hidRefKey'] = $rsHeader[0]['pkey'];
                    $arr['hidVoucherKey'] = $rsVoucher[0]['pkey'];
                    $arr['hidRefCustomerKey'] = $referallCustomerKey;
                    $arr['hidCustomerKey'] = $customerkey; 
                    $arr['refTableType'] = $rsTableKey['key'];
                    $arr['refCode'] = $rsHeader[0]['code'];
                    $arr['hidSaveAndProceed'] = 1;  
                
                    $arrResult = $voucherTransaction->addData($arr); 
                
                    //$voucherTransaction->changeStatus($arrResult[0]['data']['pkey'],2); 
                    
                    //$this->setLog($arrResult,true);
            }
        }
      
        
    } 
    
    
    function validateConfirm($rsHeader){ 
        $membership = new Membership();
        $membershipkey = $rsHeader[0]['membershipkey'];
        
        $rsMembership = $membership->getDataRowById($membershipkey);
        
        if($rsMembership[0]['statuskey'] != 1)
            $this->addErrorLog(false, $this->errorMsg['membership'][1]);
        
        
    } 
    
       
    function validateCancel($rsHeader, $autoChangeStatus = false){

        $membershipAttendance = new MembershipAttendance();     
        $id = $rsHeader[0]['pkey'];
        $rsMembership = $membershipAttendance->searchData('','',true,' and customermembershipkey = '.$this->oDbCon->paramString($id).' and '.$membershipAttendance->tableName.'.statuskey in (2,3)');
        if(!empty($rsMembership)) 
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. ' . $this->errorMsg[201].' ' .$this->errorMsg['membershipAttendance'][2],true);

     } 
     
    function cancelTrans($rsHeader,$copy){  

		$id = $rsHeader[0]['pkey'];
        
        // voucher gk bisa di validatecancel karena ad kemungkinan voucher sudah digunakan, dan gk mungkin dicancel
          
        $voucherTransaction = new VoucherTransaction();
        $rsVoucher = $voucherTransaction->searchData('','',true,' and refkey = '.$this->oDbCon->paramString($id).' and '.$voucherTransaction->tableName.'.statuskey in (1,2)');
     
        for($i=0;$i<count($rsVoucher);$i++) { 
            $arrayToJs = $voucherTransaction->changeStatus($rsVoucher[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }  

        
        $membershipAttendance = new MembershipAttendance();
        $rsMembership = $membershipAttendance->searchData('','',true,' and customermembershipkey = '.$this->oDbCon->paramString($id).' and '.$membershipAttendance->tableName.'.statuskey = 1');
    
        for($i=0;$i<count($rsMembership);$i++) { 
            $arrayToJs = $membershipAttendance->changeStatus($rsMembership[$i]['pkey'],4,'',false, true);
            if (!$arrayToJs[0]['valid'])
                throw new Exception('<strong>'.$rsMembership[0]['code'] . '</strong>. '.  $arrayToJs[0]['message']);    
        }
        

        $sql = 'update '.$this->tableName.' set activationdate = \'0000-00-00\', expdate = \'0000-00-00\' where pkey = ' . $rsHeader[0]['pkey']; 
        $this->oDbCon->execute($sql); 
        
        
		if ($copy)
			$this->copyDataOnCancel($id);	  
         
	} 
	  
    function generateDefaultQueryForAutoComplete($returnField){ 
            $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value
				from 
				    '.$this->tableName . ',
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
        
        return $sql;
    }
	
	
    function updateAttendance($pkey){ 
        $membershipAttendance = new MembershipAttendance();
        $rsHeader = $this->getDataRowById($pkey);   
        
        $sql = 'select COUNT(*) as qty
            from 
                '. $membershipAttendance->tableName . '
            where 
                 '. $membershipAttendance->tableName . '.customermembershipkey = '. $this->oDbCon->paramString($pkey) .' and
                 '. $membershipAttendance->tableName . '.statuskey in (2,3) ';
        
        $rsTotal = $this->oDbCon->doQuery($sql); 
        
        
        $sql = 'update  '. $this->tableName.'   set   attendance = '. $this->oDbCon->paramString($rsTotal[0]['qty']).'   where   pkey = '.$this->oDbCon->paramString($pkey); 
        $this->oDbCon->execute($sql); 
                
        $sql = 'select * from ' . $this->tableName.' where pkey = '.$this->oDbCon->paramString($pkey).' and  attendance < maxattendance'; 
		$rs = $this->oDbCon->doQuery($sql);
           
        $statuskey = (empty($rs)) ? 3 : 2;

        if ($rsHeader[0]['statuskey'] <> $statuskey)
            $this->changeStatus($pkey,$statuskey,'',true);
      
    }
    
        
    function afterStatusChanged($rsHeader){ 
        if(!empty($rsHeader[0]['voucherkey'])){ 
            $voucherTransaction = new VoucherTransaction();
            $voucherTransaction->updateVoucherAvailability($rsHeader[0]['voucherkey']); 
        }
    }
    
    function afterUpdateData($arrParam, $action){  
         if(isset($arrParam['selVoucher']) && !empty($arrParam['selVoucher'])){ 
            $voucherTransaction = new VoucherTransaction();
            $voucherTransaction->updateVoucherAvailability($arrParam['selVoucher']); 
        }
    }
    

	function normalizeParameter($arrParam, $trim = false){
		
        // set ulang nilai
        $membership = new Membership();
        $rsMembership = $membership->getDataRowById($arrParam['selMembership']);
        
        $arrParam['maxAttendance'] = $rsMembership[0]['maxattendance'];
        $arrParam['timeLimit'] = $rsMembership[0]['timelimit'];
        $arrParam['registrationCost'] = $rsMembership[0]['price'];
        
        $arrParam = parent::normalizeParameter($arrParam,true);
		
		return $arrParam;
	}
    
}
?>
