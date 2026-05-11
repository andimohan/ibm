<?php
class Diagnose extends Category{

    function __construct()
    {

        parent::__construct();

        $this->tableName = 'diagnose';
        $this->securityObject = 'Diagnose';
 
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category', 'title' => 'category', 'dbfield' => 'name', 'default' => true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 70));

        $this->includeClassDependencies(array(
            'Category.class.php'
        ));

        $this->overwriteConfig();
    }
	
	 function generateDefaultQueryForAutoComplete($returnField){ 
        $sql = 'select
                '.$returnField['key'].',
                '.$returnField['value'].' as value  ,
                '.$this->tableName . '.code,
				concat ('.$this->tableName . '.code,\' - \','.$this->tableName . '.name ) as codename
            from 
                '.$this->tableName . ',
                '.$this->tableStatus.'  
            where  		
                '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
        ';
        
        return $sql;
    }
	
 
	function searchDataForAutoComplete($returnField, $searchOptions,$orderCriteria=''){ 
        // karena category masih pake model lama, jadi harus copas dr base class
		
        $fieldname = (isset($searchOptions['field'])) ?  $searchOptions['field'] : '';
        $searchkey = (isset($searchOptions['key'])) ?  $searchOptions['key'] : '';
        $searchCriteria = (isset($searchOptions['criteria'])) ? $searchOptions['criteria'] : '';
             
        // update disini, karena  di generateDefaultQueryForAutoComplete repot, sering overwrite di class lain.
        if (is_array($returnField['value'])){
              $temp = array();

              // $obj->tableName.'.'. was temporary fix
              for($k=0;$k<count($returnField['value']);$k++){ 
                $colName = $returnField['value'][$k];
                 if( strpos($colName, '.') === false)  $colName =  $this->tableName.'.'.$colName;
                 array_push($temp,$colName); 
              }
            

             $returnField['value'] = 'concat('.implode('," - ",', $temp).')'; 
        } 
         
		$sql = $this->generateDefaultQueryForAutoComplete($returnField);
	
          
		if(!empty($fieldname)){  
            if (is_array($fieldname)){
                $tempArray = array();
                
                //$obj->tableName.'.' as temporary fixed
                for($i=0;$i<count($fieldname);$i++)
                    array_push($tempArray,$this->tableName.'.'. $this->oDbCon->paramOrder($fieldname[$i]) . ' like '. $this->oDbCon->paramString('%'.$searchkey.'%') );
                
                $tempSql = implode(' or ' , $tempArray);
            }else{ 
                $tempSql =  $this->oDbCon->paramOrder($fieldname) .' like '. $this->oDbCon->paramString('%'.$searchkey.'%'); 
            } 
            
            $sql .= ' and (' . $tempSql . ')'; 
		}
				
		if($searchCriteria <> '')
			$sql .= ' ' .$searchCriteria;
	
		if($orderCriteria <> '') 
			$sql .= ' ' .$orderCriteria;
		 
         $rs = $this->oDbCon->doQuery($sql);	
         for($i=0;$i<count($rs);$i++) 
            $rs[$i]['value'] = htmlspecialchars_decode($rs[$i]['value']); 
             
         return $rs;
	} 

 
}