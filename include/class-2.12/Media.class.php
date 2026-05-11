<?php

class Media extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'media';
    $this->tableStatus = 'master_status';
    $this->securityObject = 'Media';
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey');  
    $this->arrData['code'] = array('code');
    $this->arrData['name'] = array('name');
    $this->arrData['statuskey'] = array('selStatus');
 

    $this->arrDataListAvailableColumn = array(); 
    array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
    array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
    array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
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

    function validateForm($arr,$pkey = ''){ 

        $arrayToJs = parent::validateForm($arr,$pkey); 
        
        return $arrayToJs;
    }

    
    function normalizeParameter($arrParam, $trim=false){
                    

        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }


    

}

?>
