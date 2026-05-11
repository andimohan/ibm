<?php
class Port extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'port';
		$this->tableCity = 'city';
		$this->tableCityCategory = 'city_category';
		$this->tableCost = 'multiple_cost_detail';  
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'Port'; 
	   
	   	$this->newLoad = true;
		
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['partnerid'] = array('partnerID'); 
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['tag'] = array('tag');   
        $this->arrData['citykey'] = array('hidCityKey'); 
       
        $this->arrLockedTable = array();  
        array_push($this->arrLockedTable, array('table'=>'emkl_job_order_header','field'=>'podkey')); 
        array_push($this->arrLockedTable, array('table'=>'emkl_job_order_header','field'=>'polkey')); 
        array_push($this->arrLockedTable, array('table'=>'emkl_order_header','field'=>'podkey')); 
        array_push($this->arrLockedTable, array('table'=>'emkl_order_header','field'=>'polkey')); 
        array_push($this->arrLockedTable, array('table'=>'emkl_order_header','field'=>'portkey')); 
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'city','title' => 'city','dbfield' => 'cityname','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'tag','title' => 'tag','dbfield' => 'tag','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
      
		$this->overwriteConfig();
	}
 
    
    function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
					'.$this->tableCity.'.name as cityname,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.'
					left join '. $this->tableCity .' on '. $this->tableName .'.citykey = '. $this->tableCity .'.pkey,
                    '.$this->tableStatus.' 
				where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
   
   
    
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey);

		$name = $arr['name'];   
        
	 	$rs = $this->isValueExisted($pkey,'name',$name);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['port'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['port'][2]);
		} 
        
		return $arrayToJs;
	 }	 
	   
    
    
	
 
}
?>
