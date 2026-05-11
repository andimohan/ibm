<?php

class PaymentType extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'payment_type';  
	    $this->tableLangValue = 'payment_type_lang';
		$this->securityObject = 'paymentType'; 
		$this->tableStatus = 'master_status'; 
        
 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');
       
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'title','dbfield' => 'title','default'=>true, 'width' => 150));
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
				'.$this->tableName . ' , '.$this->tableStatus.' 
			where  		 
				'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
   }
    
    function normalizeParameter($arrParam, $trim = false){ 
                 
        $arrParam['fileName'] = $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader']);    
        
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
         
        return $arrParam; 
    }
     
}

?>