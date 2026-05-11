<?php

class CompanyHistory extends Baseclass{
  
    function __construct($rsRunningPeriod = array()){
		
		parent::__construct();
    
		$this->tableName = 'company_history';  
	    $this->tableLangValue = 'company_history_lang';
		$this->tableStatus = 'master_status';  
		$this->securityObject = 'CompanyHistory'; 
        $this->uploadFolder = 'company-history/';
		$this->newLoad = true; 
      
         
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['statuskey'] = array('selStatus');
        $this->arrData['name'] = array('name'); 
        $this->arrData['orderlist'] = array('orderList'); 
        $this->arrData['shortdesc'] = array('shortDescription');
        $this->arrData['image'] = array('item-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader'));
        $this->arrData['trdesc'] = array('txtDescription','raw');
 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));  

        $this->overwriteConfig();
        
	}
	
	function getQuery(){
	   
	   $sql = '
				select
					'.$this->tableName. '.*, 
					'.$this->tableStatus.'.status as statusname 
				from 
					'.$this->tableName . ',
                    '.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
                    
 		' .$this->criteria ; 
		    
        return $sql;
    }
    
    function normalizeParameter($arrParam, $trim = false){
         
         $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
         $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
	 
}

?>