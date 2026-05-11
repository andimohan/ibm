<?php
class CarSeries extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'car_series';
        $this->tableBrand = 'brand';
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'CarSeries'; 
		
       	$this->arrLockedTable = array();
        $defaultFieldName = 'serieskey';
        array_push($this->arrLockedTable, array('table'=>'car','field'=>$defaultFieldName));
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name');
        $this->arrData['brandkey'] = array('hidBrandKey'); 
        $this->arrData['statuskey'] = array('selStatus');  
        
                
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'series','title' => 'series','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'brand','title' => 'brand','dbfield' => 'brandname','default'=>true, 'width' => 300));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->includeClassDependencies(array(
              'Brand.class.php',  
              'Warehouse.class.php',  
        ));
       

        $this->overwriteConfig();
        
       
	}
	
	 function getQuery(){
	   
	   return '
			select
					'.$this->tableName. '.*,
                    '.$this->tableBrand.'.name as brandname,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.',
                    '.$this->tableBrand.',
                    '.$this->tableStatus.'
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.brandkey = '.$this->tableBrand.'.pkey
 		' .$this->criteria ; 
		 
    }
    
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey);

		$name = $arr['name']; 
        $brand = $arr['hidBrandKey'];
		
        $rs = $this->isValueExisted($pkey,'name',$name);
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['series'][1]);
		}else if(count($rs) <> 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['series'][2]);
		}
        
        if(empty($brand))
            $this->addErrorList($arrayToJs,false,$this->errorMsg['brand'][1]);
        
		return $arrayToJs;
	 }	 
	  
    function generateDefaultQueryForAutoComplete($returnField){ 
            $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value
				from 
				    '.$this->tableName . ',
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
			';
        
        return $sql;
    }
    
}
?>
