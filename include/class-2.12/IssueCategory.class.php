<?php 
class IssueCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'issue_category';  
		$this->securityObject = 'IssueCategory'; 
		$this->uploadFolder = 'issue-category/'; 
	 
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'issue','field'=>$defaultFieldName)); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'parent','title' => 'parent','dbfield' => 'parentname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
       
		$this->overwriteConfig();
        
   }
    
	   
	 function validateForm($arr,$pkey = ''){
		     
		$arrayToJs = BaseClass::validateForm($arr,$pkey); 
		 
		$name = $arr['name'];  
        $category = $arr['selCategory'];
		$orderlist = $this->unformatNumber($arr['orderList']);    
        
        $pkeyCriteria = (!empty($pkey)) ? ' and '.$this->tableName.'.pkey <> ' . $this->oDbCon->paramString($pkey) : ''; 
         
        $rsName = $this->searchData('','',true,' and '.$this->tableName.'.name = '.$this->oDbCon->paramString($name).' and '.$this->tableName.'.parentkey = '.$this->oDbCon->paramString($category).' '.$pkeyCriteria);
           
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		} else{ 
            if (count($rsName) <> 0 ){ 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]); 
            }
        }
		
		if (!empty($orderlist)){
			if (!is_numeric($orderlist)){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['orderList'][2]);
			}
		}
		  
		return $arrayToJs;
	 }
 
}

?>