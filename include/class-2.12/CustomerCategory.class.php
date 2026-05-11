<?php

class CustomerCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'customer_category';  
		$this->securityObject = 'CustomerCategory'; 
	 
        $this->arrData['requestid'] = array('requestid');
	   
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'customer','field'=>$defaultFieldName)); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        
        $this->overwriteConfig();
   }
	
	function afterUpdateData($arrParam, $action){
        parent::afterUpdateData($arrParam,$action); 
		
		$pkey = $arrParam['pkey'];
		// kalo neglink ke sistem lain 
		// kalo auto add ke TMS
		$this->syncPartnerCustomerCategory($pkey);
    }
     
	function syncPartnerCustomerCategory($pkey){
		if(empty(PARTNER_ACCOUNT['TMS']))  return;
			 
        $rsHeader = $this->searchData( $this->tableName.'.pkey',$pkey,true); 
 
		// add to API
		$url = PARTNER_ACCOUNT['TMS']['partnerurl'].'/api/v3/customer-categories';
		
		// harus dipisah karena kalo ad request_id, ketika PUT, kodenya gk muncul
		$method = 'PUT'; // ( !empty($rsHeader[0]['partnerid']) ) ? 'PUT' : 'POST';
		
		$payload = array();
		$payload['code'] = $rsHeader[0]['code']; 
		$payload['request_id'] = $pkey;
		$payload['name'] = $rsHeader[0]['name'];  
		$payload['status_key'] = $rsHeader[0]['statuskey'];
		
		$this->executeAPIPartner(PARTNER_ACCOUNT['TMS'], $url,$method,$payload);
		 
	} 
	
     
}

?>