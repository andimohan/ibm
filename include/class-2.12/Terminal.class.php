<?php
class Terminal extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'terminal';
		//$this->tableNameDetail = 'multiple_cost_detail';
		$this->tableCity = 'city';
		$this->tableItem = 'item';
		$this->tableCityCategory = 'city_category';
		$this->tableCost = 'multiple_cost_detail';  
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'Terminal'; 
	   
		$this->newLoad = true;
	   
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailItemKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');  
        $this->arrDataDetail['jobcategorykey'] = array('selJobCategory'); 
        $this->arrDataDetail['reftabletype'] = array('refTableType'); 
        $this->arrDataDetail['costkey'] = array('hidItemKey'); 
        $this->arrDataDetail['servicekey'] = array('selService'); 
        $this->arrDataDetail['price'] = array('price','number');  
       
        $arrDetails = array(); 
        array_push($arrDetails, array('dataset' => $this->arrDataDetail, 'tableName' => $this->tableCost)); 
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey',array('dataDetail' => $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['citykey'] = array('hidCityKey'); 
        $this->arrData['address'] = array('address'); 
        $this->arrData['partnerid'] = array('partnerID'); 
        $this->arrData['requestid'] = array('requestid');   
        $this->arrData['statuskey'] = array('selStatus');   
       
        $this->arrLockedTable = array();
        $defaultFieldName = 'terminalkey'; 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=>$defaultFieldName)); 
       
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableCost,'field' => array('refkey'=>'{id}', 'reftable'=>$this->tableName)));  
        //array_push($this->arrDeleteTable, array('table'=>$this->tableNameDetail,'field' => array('refkey'=>'{id}')));  
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'city','title' => 'city','dbfield' => 'citycategoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'address','title' => 'address','dbfield' => 'address',  'width' => 200));
         
	   $this->includeClassDependencies(array(
              'City.class.php',
              'Service.class.php',
              'Location.class.php',
              'TruckingServiceOrderCategory.class.php'
        ));

		$this->overwriteConfig();
	}
	
	 function getQuery(){ 
	   return '
			select
					'.$this->tableName. '.*,
                    concat('.$this->tableCity.'.name, ", ",'.$this->tableCityCategory.'.name) as citycategoryname,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.'
                        left join '.$this->tableCity.' on '.$this->tableName.'.citykey =  '.$this->tableCity.'.pkey 
                        left join '.$this->tableCityCategory.' on '.$this->tableCity.'.categorykey =  '.$this->tableCityCategory.'.pkey,
                    '.$this->tableStatus.' where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
    }
   
    
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey);

		$name = $arr['name'];   
        
	 	$rs = $this->isValueExisted($pkey,'name',$name);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['terminal'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['terminal'][2]);
		} 
        
		return $arrayToJs;
    } 
    
     function getCost($id,$tableName = ''){ 
        $tableName = (empty($tableName)) ? $this->tableName : $tableName; 
        
        $rsKey = $this->getTableKeyAndObj($this->tableName,array('key'));
        
        $sql = 'select 
                    '. $this->tableCost.'.*,
                    '. $this->tableItem.'.name as costname 
                from 
                    '. $this->tableCost.',
                    '. $this->tableItem.'
                where  
                    '. $this->tableCost.'.reftabletype = '.$this->oDbCon->paramString($rsKey['key']).' and  
                    '. $this->tableCost.'.costkey = '. $this->tableItem.'.pkey and  
                    '. $this->tableCost.'.refkey = '.$this->oDbCon->paramString($id);
         
        return $this->oDbCon->doQuery($sql); 
    } 
     
    function normalizeParameter($arrParam, $trim=false){
        
        $rskey =  $this->getTableKeyAndObj($this->tableName,array('key')); 
        
        for($i=0;$i<count($arrParam['hidDetailItemKey']);$i++)
            $arrParam['refTableType'][$i] = $rskey['key'];
        
        $arrParam = parent::normalizeParameter($arrParam,true);  

        return $arrParam;
    }
	 
	function afterUpdateData($arrParam, $action){ 
		$pkey = $arrParam['pkey'];
		
		// kalo neglink ke sistem lain 
		// kalo auto add ke TMS
		$this->syncPartnerTerminal($pkey); 
    }
		
	function syncPartnerTerminal($pkey){
		if(empty(PARTNER_ACCOUNT['TMS']))  return;
			  
        $rsHeader = $this->searchData( $this->tableName.'.pkey',$pkey,true); 
  
		// add to API
		$url = PARTNER_ACCOUNT['TMS']['partnerurl'].'/api/v3/terminal';
		
		// harus dipisah karena kalo ad request_id, ketika PUT, kodenya gk muncul
		$method = 'PUT'; // ( !empty($rsHeader[0]['partnerid']) ) ? 'PUT' : 'POST';
		
		$payload = array();
		$payload['code'] = $rsHeader[0]['code']; 
		$payload['request_id'] = $pkey;
		$payload['name'] = $rsHeader[0]['name'];
		$payload['city_id'] = $this->getPartnerId(new City(),$rsHeader[0]['citykey']); 
		$payload['address'] = $rsHeader[0]['address']; 
		$payload['status_key'] = $rsHeader[0]['statuskey'];
		
		$this->executeAPIPartner(PARTNER_ACCOUNT['TMS'], $url,$method,$payload);   
		  
	} 
 
 
}
?>