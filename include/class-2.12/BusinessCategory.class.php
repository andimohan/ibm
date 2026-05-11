<?php
class BusinessCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'business_category';  
		$this->securityObject = 'BusinessCategory'; 
		 
        $this->newLoad = true; 
	   
        $this->arrLockedTable = array();
        $defaultFieldName = 'businesskey'; 
        array_push($this->arrLockedTable, array('table'=>'customer','field'=>$defaultFieldName)); 
                   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 150)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'shortdescription',  'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
		$this->overwriteConfig();
        
        $this->includeClassDependencies(array(
              'Customer.class.php',  
        ));
	}
	  
	function validateDelete($id,$forceDelete = false){
		    
		$arrayToJs = array();
		$rs = $this->getDataRowById($id);
		
		if ($rs[0]['systemVariable'] == 1)  {
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['name'].'</strong>. ' . $this->errorMsg[211]);
			return $arrayToJs;
		}
		
	
		$customer = new Customer();
		$rsItem = $customer->searchData($customer->tableName.'.businesskey', $id,true);  
		if(!empty($rsItem)){
			$rsCategory = $this->getDataRowById($id);
			$this->addErrorList($arrayToJs,false,'<strong>'.$rsCategory[0]['name']. '</strong>. '. $this->errorMsg[900] .' <strong>(' . $rsItem[0]['code'] . ' - ' . $rsItem[0]['name'] . ')</strong>.'); 
		}
	 
		return $arrayToJs;
	  
	 }
    
	  
}
		
?>