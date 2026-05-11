<?php
class WarrantyPeriod extends BaseClass{
     
   function __construct(){
		
		parent::__construct();
 
		$this->tableName = 'warranty_period';  
		$this->securityObject = 'WarrantyPeriod'; 
		$this->tableStatus = 'master_status';  
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['period'] = array('period','number'); 
        $this->arrData['statuskey'] = array('selStatus'); 
           
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'period','title' => 'warrantyPeriod','dbfield' => 'period','default'=>true, 'width' => 130, 'align' => 'right', 'format' => 'number'));
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
		$duedays = $this->unFormatNumber($arr['period']);  
	 	 
				 
	 	$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
		}
		 
		/*if (!is_numeric($duedays) || $duedays <= 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['warrantyPeriod'][2]);
		}*/
        
		return $arrayToJs;
	 } 
	
	   
}
		
?>