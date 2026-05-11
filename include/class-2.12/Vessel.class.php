<?php

class  Vessel extends BaseClass{ 
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'vessel'; 
		$this->tableStatus = 'master_status';  
		$this->securityObject = 'Vessel';	  
		 	 
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['flag'] = array('flag'); 
        $this->arrData['statuskey'] = array('selStatus');  
       
	    $this->newLoad = true;
       	$this->arrLockedTable = array();
        $defaultFieldName = 'vesselkey';
        array_push($this->arrLockedTable, array('table'=>'emkl_job_order_header','field'=>$defaultFieldName)); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
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
		}else{ 
            if (count($rsName) <> 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]); 
        } 
        
		return $arrayToJs;
	 }   
    
  }

?>