<?php

class ServiceCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'service_category';  
		$this->securityObject = 'ServiceCategory'; 
		$this->uploadFolder = 'service-category/'; 
	          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'shortdescription',  'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->includeClassDependencies(array( 
              'Item.class.php',  
        ));

        $this->overwriteConfig();
   }
      
 
	function validateDelete($id,$forceDelete = false){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
		
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]);
			return $arrayToJs;
		}
		
	   // harus manual, karena tipenya beda
		$service = new Service(SERVICE);
		$rsService = $service->searchData($service->tableName.'.categorykey', $id,true);   
		if(!empty($rsService)){
			$rsCategory = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rsCategory[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsService[0]['code'] . ' - ' . $rsService[0]['name'] . ')</strong>.'); 
		}
        
        $service = new Service(TRUCKING_SERVICE);
		$rsService = $service->searchData($service->tableName.'.categorykey', $id,true);   
		if(!empty($rsService)){
			$rsCategory = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rsCategory[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsService[0]['code'] . ' - ' . $rsService[0]['name'] . ')</strong>.'); 
		}
        
		return $arrayToJs;
	  
	 }
	      
   
	    
}

?>
