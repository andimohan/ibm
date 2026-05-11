<?php

class Membership extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'membership'; 
		$this->tableStatus = 'master_status';
		$this->securityObject = 'Membership';
		
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey'); 
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['maxattendance'] = array('maxAttendance','number');
        $this->arrData['timelimit'] = array('timeLimit','number');
        $this->arrData['price'] = array('price','number');       
        $this->arrData['statuskey'] = array('selStatus'); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));        
        array_push($this->arrDataListAvailableColumn, array('code' => 'maxattendance','title' => 'maxAttendance','dbfield' => 'maxattendance','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));               
        array_push($this->arrDataListAvailableColumn, array('code' => 'timelimit','title' => 'timeLimit','dbfield' => 'timelimit','default'=>true, 'width' => 100, 'align'=>'right', 'format' => 'number'));            
        array_push($this->arrDataListAvailableColumn, array('code' => 'price','title' => 'price','dbfield' => 'price','default'=>true, 'width' => 100, 'align' => 'right', 'format'=>'number'));                   
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->overwriteConfig();
       
   }
    
	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,  
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ',
					'.$this->tableStatus.'
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
        
        ' .$this->criteria ;
                
        return $sql;
    }
    
	function validateForm($arr,$pkey = ''){ 
        
        $arrayToJs = parent::validateForm($arr,$pkey);  
        
        $name = $arr['name']; 

		 
		return $arrayToJs;
	 } 
    
    
    function normalizeParameter($arrParam, $trim = false){  
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
        
    }
     
  }

?>