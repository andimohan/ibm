<?php
// PricingCategory, rencana bisa diturunkan nanti 

class PricingCategory extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'pricing_category';  
		$this->securityObject = 'PricingCategory'; 
		$this->tableStatus = 'master_status';
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['iscaratbase'] = array('chkCaratBase');
	   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
        $this->newLoad=true;
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
	 	 
	 	$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['currency'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['currency'][2]);
		}
		  	
		return $arrayToJs;
	 } 
	 
}
		
?>
