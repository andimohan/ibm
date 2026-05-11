<?php
class FAQ extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'faq_header';   
		$this->tableStatus = 'master_status';
		$this->securityObject = 'FAQ'; 

        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['question'] = array('question');
        $this->arrData['answer'] = array('answer','raw'); 
        $this->arrData['orderlist'] = array('orderList','number');
        $this->arrData['statuskey'] = array('selStatus');
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'question','title' => 'question','dbfield' => 'question','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

        $this->newLoad = true;
       
        $this->overwriteConfig();
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.','.$this->tableStatus.' where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey
 		' .$this->criteria ; 
		 
    }
         
	function validateForm($arr,$pkey = ''){
		    
        $order = $arr['orderList']; 
        $question = $arr['question']; 
        $answer = $arr['answer']; 

		$arrayToJs = parent::validateForm($arr,$pkey); 
        
        if(empty($question)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['question'][1]);

        }
        
        if(empty($answer)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['answer'][1]);

        }
        
        
        if (!is_numeric($order)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['orderList'][2]);
		}
        
		return $arrayToJs;
	 }	 
	   

    function normalizeParameter($arrParam, $trim = false){ 
                 
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
		
}
?>
