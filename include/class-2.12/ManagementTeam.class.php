<?php

class  ManagementTeam extends BaseClass{ 
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'management_team'; 
		$this->tableStructure = 'management_structure'; 
		$this->tableStatus = 'master_status';
	    $this->tableLangValue = 'management_team_lang';
		$this->securityObject = 'ManagementTeam';	  
        $this->uploadFolder = 'management-team/';

        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
       
        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['structurekey'] = array('selStructure'); 
        $this->arrData['position'] = array('position');    
        $this->arrData['shortdesc'] = array('shortDescription');        
        $this->arrData['trdesc'] = array('txtDescription','raw');        
        $this->arrData['image'] = array('item-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-image-uploader', 'fileName' => 'item-image-uploader'));
        $this->arrData['orderlist'] = array('orderList','number'); 
          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'division','title' => 'division','dbfield' => 'structurename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'position','title' => 'position','dbfield' => 'position','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       

        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name')); 
        array_push($this->arrSearchColumn, array('Posisi', $this->tableName . '.position')); 
        array_push($this->arrSearchColumn, array('Struktur', $this->tableStructure . '.name')); 


        $this->newLoad = true;
        $this->overwriteConfig();
   }
   
	
   function getQuery(){
	    
	   $sql = '
			select
					'.$this->tableName. '.*, 
					'.$this->tableStructure.'.name as structurename, 
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName .', 
					'.$this->tableStructure .', 
					'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and
					'.$this->tableName . '.structurekey = '.$this->tableStructure.'.pkey  
                    
 		' .$this->criteria ; 
		  
       return $sql;
    }  

    
	function validateForm($arr,$pkey = ''){
		  
        $arrayToJs = parent::validateForm($arr,$pkey); 
         	 	
		$name = $arr['name'];  
         
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}
        
		return $arrayToJs;
	 }   
    
    
    
     function normalizeParameter($arrParam, $trim = false){ 
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }
    
  }

?>
