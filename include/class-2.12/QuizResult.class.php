<?php

class QuizResult extends BaseClass{
	
    function __construct(){

    parent::__construct();

    $this->tableName = 'quiz_result';
    $this->tableQuiz = 'quiz_header';
    $this->tableWarehouse = 'warehouse';   
    $this->tableStatus = 'master_status';
    $this->securityObject = 'Quiz';

//    $this->arrData = array(); 
//    $this->arrData['pkey'] = array('pkey');  
//    $this->arrData['code'] = array('code');
//    $this->arrData['warehousekey'] = array('selWarehouseKey');
//    $this->arrData['customerkey'] = array('hidCustomerKey');
//    $this->arrData['trdate'] = array('trDate','date');
//    $this->arrData['name'] = array('name');
//    $this->arrData['email'] = array('email');
//    $this->arrData['phone'] = array('phone');
//    $this->arrData['rightanswer'] = array('rightAnswer');
//    $this->arrData['wronganswer'] = array('wrongAnswer');
//    $this->arrData['description'] = array('description');
//    $this->arrData['statuskey'] = array('selStatus');
 

    $this->arrDataListAvailableColumn = array(); 
//        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'quiz','title' => 'quiz','dbfield' => 'quizname','default'=>true));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'email','title' => 'email','dbfield' => 'email','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'phone','title' => 'phone','dbfield' => 'phone','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'rightanswer','title' => 'correct','dbfield' => 'rightanswer','default'=>true, 'width' => 70, 'align'=> 'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'wronganswer','title' => 'incorrect','dbfield' => 'wronganswer','default'=>true, 'width' => 70,'align'=> 'center'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
    }

    function getQuery(){
        
        $sql = '
            SELECT
                '.$this->tableName.'.*, 
                '.$this->tableQuiz.'.name as quizname,
                '.$this->tableStatus.'.status as statusname
                
            FROM 
                '.$this->tableName.',
                '.$this->tableQuiz.',
                '.$this->tableStatus.'
            WHERE
                '.$this->tableName.'.refkey =  '.$this->tableQuiz.'.pkey and
                '.$this->tableName.'.statuskey =  '.$this->tableStatus.'.pkey

                 
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
