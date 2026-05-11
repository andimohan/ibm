<?php

class EventCategory extends Category{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'event_category';  
		$this->securityObject = 'EventCategory';  
		 
        $this->includeClassDependencies(array( 
            'Event.class.php'
        ));
   }
     
 
	function validateDelete($id,$forceDelete = false){
		    
		$arrayToJs = array();
		
		$event = new Event();
		$rsData = $event->searchData($event->tableName.'.categorykey', $id,true); 
				
		if(!empty($rsData)){
			$rs = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsData[0]['code'] . ' - ' . $rsData[0]['title'] . ')</strong>.');
		}
	 
		return $arrayToJs;
	 }
	    
}

?>