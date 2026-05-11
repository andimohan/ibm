<?php
class MembershipAttendance extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'membership_attendance';
        $this->tableCustomerMembership = 'customer_membership';
        $this->tableMember = 'membership';
        $this->tableCustomer = 'customer';
        $this->tableItem = 'item';
		$this->tableStatus = 'transaction_status';  
		$this->securityObject = 'MembershipAttendance'; 
	    $this->isTransaction = true; 
		
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['servicekey'] = array('hidServiceKey');
        $this->arrData['customermembershipkey'] = array('selCustomerMembership');
	    $this->arrData['trdesc'] = array('trDesc');
	    $this->arrData['statuskey'] = array('selStatus'); 
        
        $this->allowedStatusForEdit = array(1,2);
                
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 60));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 130, 'align'=>'center', 'format' => 'datetime'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customerCode','title' => 'customerCode','dbfield' => 'customercode','default'=>true, 'width' => 110));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customerMembership','title' => 'membership','dbfield' => 'membershipname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'attendance','title' => 'attendance','dbfield' => 'attendance','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'maxAttendance','title' => 'maxAttendance','dbfield' => 'maxattendance','default'=>true, 'width' => 120, 'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'timeLimit','title' => 'timeLimit','dbfield' => 'timelimit','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));
	    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
	    array_push($this->arrDataListAvailableColumn, array('code' => 'class','title' => 'class','dbfield' => 'servicename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
 
        $this->overwriteConfig();
	}
	
	 function getQuery(){

	   return '
			select
					'.$this->tableName. '.*,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableCustomer.'.code as customercode,
                    '.$this->tableMember.'.name as membershipname,
                    concat('.$this->tableCustomerMembership.'.code," - ",'.$this->tableMember.'.name) as membershipcodename,
                    '.$this->tableCustomerMembership.'.attendance,
                    '.$this->tableMember.'.maxattendance,
                    '.$this->tableMember.'.timelimit,
                    '.$this->tableItem.'.name as servicename,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.'
                        left join '. $this->tableItem.' on ' . $this->tableName .'.servicekey = ' . $this->tableItem .'.pkey,
                    '.$this->tableCustomer.',
                    '.$this->tableCustomerMembership.',
                    '.$this->tableMember.',
                    '.$this->tableStatus.'
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.customermembershipkey = '.$this->tableCustomerMembership.'.pkey and
                    '.$this->tableCustomerMembership.'.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableCustomerMembership.'.membershipkey = '.$this->tableMember.'.pkey
 		' .$this->criteria ; 
		 
    }
	 
    
	function validateForm($arr,$pkey = ''){
        
        $customerMembership = new CustomerMembership(); 
        
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
        $rsMember = $customerMembership->getDataRowById($arr['selCustomerMembership']); 
 
        $date1 = str_replace('\'','',$this->oDbCon->paramDate($arr['trDate'],' / ','Y-m-d'));
        $trDate = strtotime($date1);
        
        $expDate = strtotime($rsMember[0]['expdate']);
        $activationDate = strtotime($rsMember[0]['activationdate']);
 
        
        $memberkey = $arr['hidCustomerKey']; 
        if(empty($memberkey))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
         

        if(empty($arr['selCustomerMembership']))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customerMembership'][1]);
        if($memberkey<>$rsMember[0]['customerkey'])
            $this->addErrorList($arrayToJs, false, $this->errorMsg['customer'][3]);
        
        if($trDate > $expDate || $trDate < $activationDate)
            $this->addErrorList($arrayToJs,false,  $this->errorMsg['customerMembership'][3]);

        
        if($rsMember[0]['attendance'] >= $rsMember[0]['maxattendance'])
            $this->addErrorList($arrayToJs,false, $this->errorMsg['maxattendance'][2]);
          
        if($rsMember[0]['statuskey'] != 2)
            $this->addErrorList($arrayToJs,false, $this->errorMsg['customerMembership'][4]);
        
		return $arrayToJs;
    }
    
    function validateConfirm($rsHeader){
        $customerMembership = new CustomerMembership();
        $rsMember = $customerMembership->getDataRowById($rsHeader[0]['customermembershipkey']);
        $trDate = strtotime($rsHeader[0]['trdate']);
        $expDate = strtotime($rsMember[0]['expdate']);
        $activationDate = strtotime($rsMember[0]['activationdate']);
        
        if($rsHeader[0]['customerkey']<>$rsMember[0]['customerkey'])
            $this->addErrorLog(false,  $this->errorMsg['customer'][3]);
        

        if($trDate > $expDate || $trDate < $activationDate )
            $this->addErrorLog(false,$rsHeader[0]['code']. '. ' . $this->errorMsg['customerMembership'][3]);

        if($rsMember[0]['attendance'] >= $rsMember[0]['maxattendance'])
            $this->addErrorLog(false, $this->errorMsg['maxattendance'][2]); 

        if($rsMember[0]['statuskey'] != 2)
            $this->addErrorLog(false, $this->errorMsg['customerMembership'][4]);    
    
    }
	
	function afterStatusChanged($rsHeader){ 
        $customerMembership = new CustomerMembership();
        $customerMembership->updateAttendance($rsHeader[0]['customermembershipkey']);
         
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
	
	function normalizeParameter($arrParam, $trim = false){
		
        //$arrParam['trDate'] = date('d / m / Y H:i');
		$arrParam = parent::normalizeParameter($arrParam);
		
		return $arrParam;
	}
    
}
?>
