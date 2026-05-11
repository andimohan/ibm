<?php

class Course extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'course_header';
    $this->tableNameDetail = 'course_detail';
    $this->tableQuiz = 'quiz_header';
    $this->tableCategory = 'course_category';  
    $this->tableWarehouse = 'warehouse';   
    $this->tableStatus = 'master_status';
    $this->uploadFolder = 'course/';
    $this->securityObject = 'Course';
 
    $this->arrDataDetail = array(); 
    $this->arrDataDetail['pkey'] = array('hidDetailKey');
    $this->arrDataDetail['refkey'] = array('pkey','ref'); 
    $this->arrDataDetail['quizkey'] = array('hidQuizKey'); 
    //$this->arrDataDetail['isquiz'] = array('chkIsQuiz');

    $arrDetails = array();
    array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        
    $this->arrData = array(); 
    $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));  
    $this->arrData['code'] = array('code');
    $this->arrData['warehousekey'] = array('selWarehouseKey');
    $this->arrData['categorykey'] = array('hidCategoryKey');
    $this->arrData['name'] = array('name');
    $this->arrData['trdate'] = array('trDate','date');
    $this->arrData['orderlist'] = array('orderList', 'number');
    $this->arrData['description'] = array('description');
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
                '.$this->tableWarehouse.'.name as warehousename,
                '.$this->tableStatus.'.status as statusname
                
            FROM '.$this->tableStatus.',
                 '.$this->tableWarehouse.',
                 '.$this->tableName.'
            WHERE   
                  '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                  '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 

            ' .$this->criteria ;
                                         
        return $sql;
    }

    function validateForm($arr,$pkey = ''){ 

        $arrayToJs = parent::validateForm($arr,$pkey); 
             $categorykey = $arr['hidCategoryKey'];

        
        if (  empty($categorykey)){ 
				$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]); 
		}
        
        return $arrayToJs;
    }

    
    function normalizeParameter($arrParam, $trim=false){
        
   
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }


     function getDetailWithRelatedInformation($pkey,$criteria=''){
        $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableQuiz.'.name as quizname
          from
            '.$this->tableQuiz.',
            '.$this->tableNameDetail.'
          where  
            '. $this->tableNameDetail.'.quizkey = '.$this->tableQuiz.'.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }

}

?>
