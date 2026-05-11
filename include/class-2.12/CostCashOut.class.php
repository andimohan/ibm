<?php

class CostCashOut extends BaseClass{
    
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'cost_cash_out';   
		$this->tableStatus = 'master_status';
        $this->tableCOA = 'chart_of_account'; 
        $this->securityObject = 'CostCashOut';
        
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['name'] = array('name'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['coakey'] = array('hidCOAHeaderKey');
                 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'coalink','title' => 'coalink','dbfield' => 'coaname','default'=>true, 'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
        $this->overwriteConfig();
    }
    
    
    function getQuery(){ 
	   
	   $sql = '
				select
					'.$this->tableName. '.*,
                    concat('.$this->tableCOA. '.code,\' - \','.$this->tableCOA. '.name) as coaname, 
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName . ' 
					   left join '.$this->tableCOA . ' on '.$this->tableName . '.coakey = '.$this->tableCOA.'.pkey ,
                    '.$this->tableStatus.' 
				where  		 
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
                    
 		' .$this->criteria ; 
		  
       return $sql;
   }
    
    
  function validateForm($arr,$pkey = ''){
		       
		$arrayToJs = parent::validateForm($arr,$pkey);  
        $chartOfAccount = new ChartOfAccount();
        
		$name = $arr['name'];     
        $arrCOAHeaderKey = $arr['hidCOAHeaderKey'];
 
		$rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['cost'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['cost'][2]);
		}
		  
        if(empty($arrCOAHeaderKey)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['coa'][1]);
		} 
	
          
		return $arrayToJs;
	 }
    
} 
  
?>