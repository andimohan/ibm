<?php
class OilType extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'oil_type';
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'OilType'; 
		
       	$this->arrLockedTable = array();
         
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');   
       
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
			$this->addErrorList($arrayToJs,false,$this->errorMsg['oilType'][1]);
		}else if(count($rs) <> 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['oilType'][2]);
		}
        
		return $arrayToJs;
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
    
}
?>