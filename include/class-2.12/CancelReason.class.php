<?php
class CancelReason extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'cancel_reason';   
		$this->tableStatus = 'master_status';
		$this->securityObject = 'CancelReason'; 

        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['reason'] = array('reason');
        $this->arrData['orderlist'] = array('orderList','number');
        $this->arrData['statuskey'] = array('selStatus');
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'format'=>'date','width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reason','title' => 'cancelReason','dbfield' => 'reason','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

       
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
        $reason = $arr['reason']; 


		$arrayToJs = parent::validateForm($arr,$pkey); 
        
        if(empty($reason)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['cancelReason'][1]);

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
