<?php
class APILog extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'api_log';
       
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'api'; 
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['ip'] = array('ip');
        $this->arrData['action'] = array('action');
        $this->arrData['endpoint'] = array('endpoint');
        $this->arrData['payload'] = array('payload','raw');
        $this->arrData['responsecode'] = array('responseCode');
        $this->arrData['responsemsg'] = array('responseMsg','raw');
		
       	$this->arrLockedTable = array(); 
       
	}
	
	 function getQuery(){
	    return '
				select
					'.$this->tableName. '.* 
				from 
					'.$this->tableName . ' 
				where
					1=1
 		' .$this->criteria ; 
	 } 
     
    
  	function normalizeParameter($arrParam, $trim = false){ 
         
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam; 
    }
    
}
?>