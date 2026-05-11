<?php

class CityCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'city_category';  
		$this->securityObject = 'CityCategory'; 
	 
        $this->arrData['requestid'] = array('requestid');
	   
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey';
        array_push($this->arrLockedTable, array('table'=>'city','field'=>$defaultFieldName)); 
             
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
             
		$this->overwriteConfig(); 
   }
    
   function getQuery($count=''){
	     
	    $select = 'select
					'.$this->tableName. '.*,
					'.$this->tableStatus.'.status as statusname,
					parentcat.code as parentcode,
					parentcat.name as parentname';

		if (!empty($count))
			$select = 'select count('.$this->tableName . '.pkey) as totalrows';
	   
	    return $select.' from
					'.$this->tableName . ' left join '.$this->tableName . ' parentcat on 	parentcat.pkey = '.$this->tableName . '.parentkey ,'.$this->tableStatus.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
	
	    
    function afterUpdateData($arrParam, $action){ 
        parent::afterUpdateData($arrParam,$action); 
		
		$pkey = $arrParam['pkey'];
	 
		// kalo neglink ke sistem lain 
		// kalo auto add ke TMS
		$this->syncPartnerCityCategories($pkey);
    }
	
	function syncPartnerCityCategories($pkey){
		if(empty(PARTNER_ACCOUNT['TMS']))  return;
			  
        $rsHeader = $this->searchData( $this->tableName.'.pkey',$pkey,true); 
  
		
		// add to API
		$url = PARTNER_ACCOUNT['TMS']['partnerurl'].'/api/v3/city-categories';
		//$this->setLog($url,true);
		
		// harus dipisah karena kalo ad request_id, ketika PUT, kodenya gk muncul
		$method = 'PUT'; // ( !empty($rsHeader[0]['partnerid']) ) ? 'PUT' : 'POST';
		
		$payload = array();
		$payload['code'] = $rsHeader[0]['code']; 
		$payload['request_id'] = $pkey;
		$payload['name'] = $rsHeader[0]['name']; 
		$payload['order_list'] = $rsHeader[0]['orderlist'];   
		$payload['parent_category_id'] = 0;   // sementara bl mad subkategori
		$payload['short_description'] = $rsHeader[0]['shortdescription']; 
		$payload['description'] = $rsHeader[0]['description'];   
		$payload['status_key'] = $rsHeader[0]['statuskey'];
		
		$this->executeAPIPartner(PARTNER_ACCOUNT['TMS'], $url,$method,$payload);   
		 
		
	} 
 
	    
}

?>