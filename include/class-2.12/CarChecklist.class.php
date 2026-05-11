<?php
class CarChecklist extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'car_checklist';     
		$this->securityObject = 'CarChecklist'; 
		$this->tableStatus = 'master_status'; 
	 
        /*$this->arrLockedTable = array();
        $defaultFieldName = 'itemkey'; 
        array_push($this->arrLockedTable, array('table'=>'item_checklist_group_detail','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'item_content_of_package_detail','field'=>$defaultFieldName)); */
        
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['statuskey'] = array('selStatus');
               
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
     
		$this->overwriteConfig();
	}
	
	 function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ', 
                    '.$this->tableStatus.'
				where  		  
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	
	 
	function validateForm($arr,$pkey = ''){ 
		     
		$arrayToJs = parent::validateForm($arr,$pkey);  
		   
	 	$name = $arr['name'];    
        
        $rsName = $this->isValueExisted($pkey,'name',$name);	
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		} else{ 
            if (count($rsName) <> 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]); 
        }
        
		return $arrayToJs;
	 } 
	 
}
		
?>
