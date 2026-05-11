<?php

class Banner extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'banner';  
		$this->tableNamePosition = 'banner_position';   
	    $this->tableLangValue = 'banner_lang';
		$this->securityObject = 'Banner'; 
		$this->tableStatus = 'master_status';
		$this->uploadFolder = 'banner/'; 
		
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataLang, 'tableName' => $this->tableLangValue));
		
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['url'] = array('url');
        $this->arrData['poskey'] = array('selPosition');
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['orderlist'] = array('orderList'); 
        $this->arrData['isloop'] = array('chkIsLoop'); 
        $this->arrData['file'] = array('item-file-uploader',array('datatype' => 'file', 'uploadFolder' => $this->uploadFolder,  'token' => 'token-item-file-uploader', 'fileName' => 'item-file-uploader'));
        $this->arrData['trdesc'] = array('txtDetail','raw'); 
       
	   	$this->newLoad=true;
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'title','dbfield' => 'name', 'default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'url','title' => 'url','dbfield' => 'url','default'=>true,  'width' => 250));
      	array_push($this->arrDataListAvailableColumn, array('code' => 'position','title' => 'position','dbfield' => 'positionname','default'=>true,  'width' => 200)); 
      	array_push($this->arrDataListAvailableColumn, array('code' => 'statusname','title' => 'status','dbfield' => 'statusname','default'=>true,  'width' => 100)); 
	   
	 
		$this->arrSearchColumn = array ();
        array_push($this->arrSearchColumn, array('Kode', $this->tableName . '.code'));
        array_push($this->arrSearchColumn, array('Nama', $this->tableName . '.name'));
        array_push($this->arrSearchColumn, array('URL', $this->tableName . '.url') );
        array_push($this->arrSearchColumn, array('Posisi', $this->tableNamePosition . '.name'));
   }
   
   function getQuery(){
	   
	   $sql = '
				select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname ,
					'.$this->tableNamePosition.'.name as positionname
				from 
					'.$this->tableName . ','.$this->tableStatus.' , '.$this->tableNamePosition.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and  
					'.$this->tableName . '.poskey = '.$this->tableNamePosition.'.pkey
 		' .$this->criteria ; 
	 
        return $sql;
   }
   
   
   
	
	 function validateForm($arr,$pkey = ''){
		
        $arrayToJs = parent::validateForm($arr,$pkey);  
         
        $name = $arr['name']; 
		$url = $arr['url']; 
		$order = $arr['orderList']; 
	 	      
        $arrImage = explode(",",$arr['item-image-uploader']);
        for($i=0;$i<count($arrImage);$i++){
            $path = $this->uploadTempDoc.$this->uploadFolder.$arr['token-item-image-uploader']; 
            if (filesize($path.'/'.$arrImage[$i]) >  (pow(1024,2) * PLAN_TYPE['maximagesize']) )
                $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][4] .' ('.$this->lang['max'].' '. $this->formatNumber(PLAN_TYPE['maximagesize']). ' MB)' );
        }
         
         
	 	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['banner'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['banner'][2]);
		}
		  
		if (!empty($url) && filter_var($url, FILTER_VALIDATE_URL) === false) {
			$this->addErrorList($arrayToJs,false,$this->errorMsg['url'][3]);
		} 
		
		if (!is_numeric($order)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['orderList'][2]);
		}
		
		return $arrayToJs;
	 }

    
	  function getAllPosition () { 
	 	 
		$sql = '
				select 
					pkey,
					name,
					concat('.$this->tableNamePosition.'.name," ", '.$this->tableNamePosition.'.bestfitsize) as namewithsize
				from 
					'.$this->tableNamePosition.'
				order by
					pkey asc
			';
			
		return $this->oDbCon->doQuery($sql); 
	 
	}   
    
    function normalizeParameter($arrParam, $trim = false){ 
                  
        $arrParam = $this->updateOthersLangValue($arrParam, $this->arrData); 
        $arrParam = parent::normalizeParameter($arrParam,true); 
          
         return $arrParam; 
    }
	
	 

}

?>