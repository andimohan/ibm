<?php  
class Waste extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'waste';
		$this->tableCategory = 'waste_category';
		$this->tableStatus = 'master_status'; 

		$this->securityObject = 'Waste';
        $this->importUrl = 'import/waste';
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['characteristic'] = array('characteristic');
        $this->arrData['categorykey'] = array('hidCategoryKey');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['trdesc'] = array('trDesc'); 
         
		$this->arrLockedTable = array();
        $defaultFieldName = 'disposalkey';
        array_push($this->arrLockedTable, array('table'=>'disposal_work_order_detail','field'=>$defaultFieldName));
               
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'characteristic','title' => 'characteristic','dbfield' => 'characteristic','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
        $this->includeClassDependencies(array(
              'Category.class.php'
        ));

		$this->overwriteConfig();
   }
	 
	 
	 
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname ,
					'.$this->tableCategory.'.name as categoryname,
                    concat ('.$this->tableName. '.code, ", ", '.$this->tableName.'.name) as wastecodename
				from 
					'.$this->tableName . ','.$this->tableStatus.' , '.$this->tableCategory.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and  
					'.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey
 		' .$this->criteria ; 
		 
    }


    function generateDefaultQueryForAutoComplete($returnField){ 
      
        $sql = 'select
                  '.$returnField['key']. ',
                  concat('.$this->tableName.'.code'. '," - ",'.$returnField['value'].') as value 
              from 
                  '.$this->tableName . ',
                  '.$this->tableStatus.'
              where  		
                  '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
          ';
      return $sql;
      
    }

    function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
	   
	 	$wastename = $arr['name'];   
	 	$categorykey = isset($arr['hidCategoryKey']) ? $arr['hidCategoryKey'] : 0;    
			 
		$rs = $this->isValueExisted($pkey,'name',$wastename); 
		if(empty($wastename)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}
		
        if(empty($categorykey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		} 
          
		 return $arrayToJs;
	 }
    
     function getWasteCategory($pkey=''){ 
       
        $sql = 'select
                    '.$this->tableCategory .'.*
               from
                   '.$this->tableCategory ;
                 
         if(!empty($pkey))
             $sql .= ' where  		
             '.$this->tableCategory . '.pkey = '.$this->oDbCon->paramString($pkey);
         
         
        $sql .=' order by name asc';
          
        return $this->oDbCon->doQuery($sql);
     
    }
 
          
    function normalizeParameter($arrParam, $trim = false){  
            
        $arrParam = parent::normalizeParameter($arrParam,true);
        return $arrParam; 
    }  

    
  }

?>
