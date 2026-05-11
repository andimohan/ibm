<?php

class Consignee extends BaseClass{ 
    
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'consignee'; 
		$this->tableStatus = 'master_status';
		$this->tableLocation = 'location'; 
		$this->securityObject = 'Consignee';	  
        $this->tableMultipleAddress = 'multiple_address_detail';	 
 
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['address'] = array('address'); 
        $this->arrData['warehousename'] = array('warehouseName'); 
        $this->arrData['locationkey'] = array('hidLocationKey'); 
        $this->arrData['contactperson'] = array('contactPerson'); 
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['requestid'] = array('requestid');      
       
       	$this->importUrl = 'import/consignee';
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'consigneekey';
        array_push($this->arrLockedTable, array('table'=>'trucking_selling_rate_header','field'=>$defaultFieldName)); 
      
        $this->arrDeleteTable = array();  
        array_push($this->arrDeleteTable, array('table'=>$this->tableMultipleAddress,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));  
       
          
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'locationname','default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'contactPerson','title' => 'contactPerson','dbfield' => 'contactperson', 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'locationname', 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address','title' => 'address','dbfield' => 'address', 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->includeClassDependencies(array( 
              'Location.class.php'  
        ));

        $this->overwriteConfig();
   }
   
	
   function getQuery(){
	    
	   $sql = '
			select
					'.$this->tableName. '.*, 
					'.$this->tableStatus.'.status as statusname	, 
					'.$this->tableLocation.'.code as locationcode , 
					'.$this->tableLocation.'.name as locationname 
				from 
					'.$this->tableName . ' 
						 left join '.$this->tableLocation.' on '.$this->tableName . '.locationkey = '.$this->tableLocation.'.pkey  
					,'.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  
 		' .$this->criteria ; 
		  
       return $sql;
    }  
	
    
    function afterUpdateData($arrParam, $action){ 
		$pkey = $arrParam['pkey'];
		
        $arrParam['maTypeKey'] = 1;
        $this->updateMultipleAddres($pkey, $arrParam); 
		
		
		// kalo neglink ke sistem lain 
		// kalo auto add ke TMS
		$this->syncPartnerConsignee($pkey);
    }
     
	    
    function updateDetail($pkey, $arrParam){  
        
    }
	 
	function validateForm($arr,$pkey = ''){
		  
        $arrayToJs = parent::validateForm($arr,$pkey); 
          
	  	$name = $arr['name'];   
	  	$location = $arr['hidLocationKey'];   
		  
        $rsName = $this->isValueExisted($pkey,'name',$name);	
	 	if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
		}else{ 
            if (count($rsName) <> 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['name'][2]); 
        } 
         
        if(empty($location)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]);
        
		return $arrayToJs;
	 }   
    
     function normalizeParameter($arrParam, $trim = false){
          
        $arrParam = parent::normalizeParameter($arrParam); 
        $arrParam['hidLocationKey'] = (!empty($arrParam['hidLocationKey'])) ? $arrParam['hidLocationKey'] : 0 ;  
          
        return $arrParam;
    }
    
	function syncPartnerConsignee($pkey){
		if(empty(PARTNER_ACCOUNT['TMS']))  return;
		
        $rsHeader = $this->searchData( $this->tableName.'.pkey',$pkey,true); 
 
	 	// add to API
		$url = PARTNER_ACCOUNT['TMS']['partnerurl'].'/api/v3/consignee';  
		
		// harus dipisah karena kalo ad request_id, ketika PUT, kodenya gk muncul
		$method = 'PUT'; // ( !empty($rsHeader[0]['partnerid']) ) ? 'PUT' : 'POST';
		
		$payload = array();
		$payload['code'] = $rsHeader[0]['code']; 
		$payload['request_id'] = $pkey;
		$payload['name'] = $rsHeader[0]['name']; 
		$payload['address'] = $rsHeader[0]['address']; 
		$payload['warehousename'] = $rsHeader[0]['warehousename']; 
		$payload['location_id'] =  $this->getPartnerId(new Location(),$rsHeader[0]['locationkey']); 
		$payload['status_key'] = $rsHeader[0]['statuskey'];
		
		$this->executeAPIPartner(PARTNER_ACCOUNT['TMS'], $url,$method,$payload);   
		 
		
	} 
  }

?>