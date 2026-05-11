<?php

class ChassisCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'chassis_category';  
		$this->securityObject = 'ChassisCategory';  
		
       	$this->arrLockedTable = array();
        $defaultFieldName = 'categorykey';
        array_push($this->arrLockedTable, array('table'=>'chassis','field'=>$defaultFieldName)); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
                         
        $this->overwriteConfig();
   } 
	    
}

?>