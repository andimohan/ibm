<?php
class InvoicePeriod extends BaseClass{
     
   function __construct(){
		
		parent::__construct();
 
		$this->tableName = 'invoice_period'; 
		$this->tableStatus = 'master_status';
	   	$this->securityObject = 'InvoicePeriod';
	   
	   	$this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');   
	   	$this->arrData['months'] = array('months','number');
                   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'month','title' => 'month','dbfield' => 'months','default'=>true, 'width' => 100 ,'align' => 'right', 'format'=>'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

        $this->overwriteConfig();
	}
	 
	 
  function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ','.$this->tableStatus.' where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ; 
		 
    }  
	
	function validateForm($arr,$pkey = ''){
		
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$name = $arr['name'];  
		$days = $this->unFormatNumber($arr['days']);  
	 	 
				 
	 	$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['invoicePeriod'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['invoicePeriod'][2]);
		}
		 
		if (!is_numeric($days) || $days < 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['invoicePeriod'][3]);
		}
		  	
		return $arrayToJs;
	 } 
	
	   
}
		
?>