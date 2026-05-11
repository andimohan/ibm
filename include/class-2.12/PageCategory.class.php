<?php

class PageCategory extends Category{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'page_category';  
		$this->securityObject = 'PageCategory';  
		
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'page','field'=>$defaultFieldName)); 
   }
	    
}

?>