<?php  
class Location extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'location';
        $this->tableCity = 'city';
        $this->tableCityCategory = 'city_category';
		$this->tableStatus = 'master_status'; 
	   
		$this->securityObject = 'Location'; 
		$this->newLoad = true; 
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name');
        $this->arrData['citykey'] = array('hidCityKey'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['requestid'] = array('requestid');      
        $this->arrData['latitude'] = array('latitude');      
        $this->arrData['longitude'] = array('longitude');   
        $this->arrData['address'] = array('address');  
        $this->arrData['maplocation'] = array('txtMapLocation');       
       
		$this->arrLockedTable = array();
        $defaultFieldName = 'locationkey';
        array_push($this->arrLockedTable, array('table'=>'depot','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=> 'stuffinglocationkey')); 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=> 'consigneelocationkey')); 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_work_order','field'=> $defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'cost_rate_header','field'=> $defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'consignee','field'=> $defaultFieldName)); 
       
		$this->importUrl = 'import/location';
	   
	   // nanti baru diaktifkan kalo ad modul job Opportunity
	   //array_push($this->arrLockedTable, array('table'=>'job_opportunities','field'=> $defaultFieldName)); 
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'city','title' => 'city','dbfield' => 'citycategoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            
        $this->includeClassDependencies(array( 
              'Category.class.php',
              'CityCategory.class.php',
              'City.class.php',  
        ));
       
		$this->overwriteConfig();
   }
	 
	 
	 
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
                    '.$this->tableCity.'.code as citycode,
                    '.$this->tableCity.'.name as cityname,
                    concat('.$this->tableCity.'.name, ", ",'.$this->tableCityCategory.'.name) as citycategoryname ,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName. ' 
                        left join  '.$this->tableCity. ' on  '.$this->tableName . '.citykey = '.$this->tableCity.'.pkey
						 left join '.$this->tableCityCategory.' on '.$this->tableCity . '.categorykey = '.$this->tableCityCategory.'.pkey ,
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		         ' .$this->criteria ; 
		 
    } 
	
	  function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
	   
	 	$locationname = $arr['name']; 
			 
		$rs = $this->isValueExisted($pkey,'name',$locationname); 
		if(empty($locationname)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]);
		}else if (count($rs) <> 0){  
			$this->addErrorList($arrayToJs,false,$this->errorMsg['location'][2]);
		} 
           
		 return $arrayToJs;
	 }
    
	  function generateDefaultQueryForAutoComplete($returnField){ 
      
          $sql = 'select
					'.$returnField['key']. ',
                    '.$returnField['value'].' as value 
				from 
					'.$this->tableName . ','.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
			';
          
        return $sql;
    }
    
     
    function normalizeParameter($arrParam, $trim=false){ 
        
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
        
	
    function afterUpdateData($arrParam, $action){ 
		$pkey = $arrParam['pkey'];
		
		// kalo neglink ke sistem lain 
		// kalo auto add ke TMS
		$this->syncPartnerLocation($pkey);
    }
     
	function syncPartnerLocation($pkey){
		if(empty(PARTNER_ACCOUNT['TMS']))  return;
			 
        $rsHeader = $this->searchData( $this->tableName.'.pkey',$pkey,true); 
 
		// add to API
		$url = PARTNER_ACCOUNT['TMS']['partnerurl'].'/api/v3/locations';
		
		// harus dipisah karena kalo ad request_id, ketika PUT, kodenya gk muncul
		$method = 'PUT'; // ( !empty($rsHeader[0]['partnerid']) ) ? 'PUT' : 'POST';
		
		$payload = array();
		$payload['code'] = $rsHeader[0]['code']; 
		$payload['request_id'] = $pkey;
		$payload['name'] = $rsHeader[0]['name']; 
		$payload['city_id'] =  $this->getPartnerId(new City(),$rsHeader[0]['citykey']); 
		$payload['status_key'] = $rsHeader[0]['statuskey'];
		
		$this->executeAPIPartner(PARTNER_ACCOUNT['TMS'], $url,$method,$payload);
		 
	} 
    
  }

?>
