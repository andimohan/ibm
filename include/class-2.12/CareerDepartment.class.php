<?php

class CareerDepartment extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'career_department';
    $this->tableStatus = 'master_status';
    $this->tableLangValue = 'career_department_lang';
    $this->securityObject = 'CareerDepartment';
       
    $arrDetails = array(); 
    array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));

    $this->arrData = array();  
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
    $this->arrData['code'] = array('code');
    $this->arrData['name'] = array('name');
    $this->arrData['statuskey'] = array('selStatus');
 
    $this->newLoad = true;

    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));


    $this->arrSearchColumn = array ();
    array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
    array_push($this->arrSearchColumn, array('Name', $this->tableName . '.name'));  

    }

    function getQuery(){
        
        
        $sql = '
            SELECT
                '.$this->tableName.'.* ,  
                '.$this->tableStatus.'.status as statusname
            FROM '.$this->tableStatus.',
                 '.$this->tableName.'
            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
            ' .$this->criteria ;
                                         
        return $sql;
    }

 
    function normalizeParameter($arrParam, $trim=false){
                    
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true);  
        return $arrParam;
    }


}

?>