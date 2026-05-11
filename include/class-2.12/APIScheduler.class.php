<?php
class APIScheduler extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'api_scheduler'; 
		$this->tableStatus = 'master_status';   
		$this->securityObject = 'APIScheduler'; 
		
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['url'] = array('url');
        $this->arrData['payload'] = array('payload','raw'); 
        $this->arrData['action'] = array('action');
        $this->arrData['jobtype'] = array('jobtype');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('url' => 'warehouse','title' => 'url','dbfield' => 'url','default'=>true, 'width' => 250));
   
        $this->printMenu = array();  
       
        $this->includeClassDependencies(array(     ));
       
		$this->overwriteConfig();
	}
	
 	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.* ,
					'.$this->tableStatus.'.status as statusname 
				from
					'.$this->tableName.' , 
                    '.$this->tableStatus.'
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
         
         
        $sql .=  $this->getWarehouseCriteria() ;
		 
         return $sql;
    }
    
	    
    
    function normalizeParameter($arrParam, $trim=false){  
        
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam; 
    }
    
}
?>