<?php

class CustomerIssue extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'customer_issue';   
		$this->tableSalesOrder = 'sales_order_header';   
		$this->tableCustomer = 'customer';   
		$this->securityObject = 'Contact'; 
		$this->tableStatus = 'master_status';
       
   	    $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['sokey'] = array('hidSOKey');
        $this->arrData['subject'] = array('subject');
        $this->arrData['issue'] = array('issue');
	   
	      
	   	$this->newLoad=true;
	   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'soCode','title' => 'soCode','dbfield' => 'salesordercode',  'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'createdon', 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name', 'width' => 100));
      	array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone', 'width' => 100));
      	array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email', 'width' => 100));
      	array_push($this->arrDataListAvailableColumn, array('code' => 'statusname','title' => 'status','dbfield' => 'statusname', 'width' => 100)); 
	   
	   
   }
   
    
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableSalesOrder.'.code as salesordercode, 
					'.$this->tableCustomer.'.name as customername, 
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ',
					'.$this->tableSalesOrder . '
							left join '.$this->tableCustomer.' on '.$this->tableSalesOrder . '.customerkey = '.$this->tableCustomer.'.pkey,
					'.$this->tableStatus.'  
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableSalesOrder . '.pkey = '.$this->tableName.'.sokey 
 		' .$this->criteria ; 
    }
	 
	
	function editData($arrParam){    
	    return ''; 
	}  
	
	function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = parent::validateForm($arr,$pkey); 
         
		$soKey = $arr['hidSOKey'];  

		if(empty($soKey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrder'][1]);
		} 
		 
		return $arrayToJs;
	}
    
    
 
    function normalizeParameter($arrParam, $trim=false){
            
            
        return $arrParam;
    }}

?>
