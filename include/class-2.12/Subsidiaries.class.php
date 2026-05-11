<?php
class Subsidiaries extends BaseClass{

    function __construct($meetingType = '1')
    {

        parent::__construct();
        $this->tableName = 'subsidiaries';
		$this->tableStatus = 'master_status';
	    $this->tableLangValue = 'subsidiaries_lang';
         
		$this->uploadFolder = 'subsidiaries/'; 
		$this->uploadCoverFolder = 'subsidiaries-cover/'; 
		$this->newLoad = true;
		
        $this->securityObject = 'Subsidiaries';
 
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));

        $this->arrData = array();  
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['detail'] = array('txtDescription','raw');
        $this->arrData['shortdesc'] = array('txtShortDesc'); 
        $this->arrData['orderlist'] = array('orderList','number');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['image'] = array('subsidiaries-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-subsidiaries-image-uploader', 'fileName' => 'subsidiaries-image-uploader'));
        $this->arrData['imagecover'] = array('subsidiaries-cover-image-uploader',array('datatype' => 'image', 'uploadFolder' => $this->uploadCoverFolder,  'token' => 'token-subsidiaries-cover-image-uploader', 'fileName' => 'subsidiaries-cover-image-uploader'));
  
        $this->arrDataListAvailableColumn = array();
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code', 'dbfield' => 'code', 'default' => true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name', 'title' => 'name', 'dbfield' => 'name', 'default' => true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status', 'title' => 'status', 'dbfield' => 'statusname', 'default' => true, 'width' => 110));


        $this->arrSearchColumn = array();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Name', $this->tableName . '.name')); 

        $this->includeClassDependencies(array(
              
        )); 
 
        $this->overwriteConfig();
    }

    function getQuery(){

        $sql = '
                 select
                     ' . $this->tableName . '.*, 
                     ' . $this->tableStatus . '.status as statusname 
                 from 
                     ' . $this->tableName . ', ' . $this->tableStatus . '
                 where  		
                     ' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey';
			
		$sql .= $this->criteria;
        
        return $sql;
    }
    
    
	function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
		 
		$name = $arr['name'];  
		$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['corporateValues'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['corporateValues'][2]);
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