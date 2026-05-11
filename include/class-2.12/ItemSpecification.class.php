<?php
class ItemSpecification extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'item_specification';  
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'ItemSpecification'; 

        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['value'] = array('value'); 
        $this->arrData['statuskey'] = array('selStatus');   
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
		$this->overwriteConfig();
   }
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.',
                    '.$this->tableStatus.' 
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
   
    
	function validateForm($arr,$pkey = ''){
		    
		$arrayToJs = parent::validateForm($arr,$pkey);

		$name = $arr['name'];   
        
	 	$rs = $this->isValueExisted($pkey,'name',$name);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]);
		} 
        
		return $arrayToJs;
	 }	 
	   
        
    
	
 
}
?>
