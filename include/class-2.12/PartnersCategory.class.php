<?php

class PartnersCategory extends Category{ 
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'partners_category';  
		$this->securityObject = 'PartnersCategory';  
		
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'partners','field'=>$defaultFieldName)); 
   }
     
 
	function validateDelete($id,$forceDelete = false){
		    
		$arrayToJs = array();
		
		$partners = new Partners();
		$rsData = $partners->searchData($news->tableName.'.categorykey', $id,true); 
				
		if(!empty($rsData)){
			$rs = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsData[0]['code'] . ' - ' . $rsData[0]['name'] . ')</strong>.');
		}
	 
		return $arrayToJs;
	 }
	    
}

?>