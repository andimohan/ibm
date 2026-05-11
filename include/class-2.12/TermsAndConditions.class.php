<?php
class TermsAndConditions extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'terms_and_conditions';  
        $this->tableJobType = 'emkl_import_export';
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'TermsAndConditions'; 
		
	   	$this->newLoad=true;
	   
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name'); 
        $this->arrData['categorykey'] = array('selCategoryKey'); 
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['content'] = array('content');
       
  
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 150));        
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'content','title' => 'content','dbfield' => 'content','default'=>true, 'width' => 70));
          
        $this->arrLockedTable = array();
        $defaultFieldName = 'termsconditionkey';
        array_push($this->arrLockedTable, array('table'=>'emkl_quotation_order_terms_and_condition_detail','field'=>$defaultFieldName));

	    $this->arrSearchColumn = array ();
	   	array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
	   	array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
	   	array_push($this->arrSearchColumn, array('Kategori', $this->tableJobType . '.name'));
	   
		$this->overwriteConfig();
        
	}
	
	 function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
					'.$this->tableJobType.'.name as categoryname, 
					'.$this->tableStatus.'.status as statusname 
				from
					'.$this->tableName.'
                        left join '. $this->tableJobType.' on ' . $this->tableName .'.categorykey = ' . $this->tableJobType .'.pkey,
                    '.$this->tableStatus.' 
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 					' .$this->criteria ;
          
         return $sql;
    }
     
      
    function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$name = $arr['name'];  
	 	 
/*	  	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['container'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['container'][2]);
		} */
		 
		return $arrayToJs;
	 }
    
 
	
	function getJobType($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableJobType .'.pkey, 
	   			'.$this->tableJobType .'.name 
              from
			  	'.$this->tableJobType .' 
			  where
			  	'.$this->tableJobType .'.statuskey = 1';
                
        if(!empty($pkey))
            $sql .= ' and pkey = '.$this->oDbCon->paramString($pkey);
        
        
//       $sql .=' order by name asc';
         
		return $this->oDbCon->doQuery($sql);
	
   }
        
  
}
?>
