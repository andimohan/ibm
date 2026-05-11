<?php

class  ManagementStructure extends BaseClass{ 
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'management_structure';  
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'ManagementStructure';	   
 
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');  
        $this->arrData['orderlist'] = array('orderList','number'); 
          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name')); 
 
        $this->newLoad = true;
        $this->overwriteConfig();
   }
   
	
   function getQuery(){
	    
	   $sql = '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName .',  
					'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey   
                    
 		' .$this->criteria ; 
		  
       return $sql;
    }  

    
	function validateForm($arr,$pkey = ''){
		  
        $arrayToJs = parent::validateForm($arr,$pkey); 
         	 	
		$name = $arr['name'];  
         
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}
        
		return $arrayToJs;
	 }   
     
    
  }

?>