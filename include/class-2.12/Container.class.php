<?php
class Container extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'container'; 
		$this->tableContainerType = 'container_type'; 
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'Container'; 
		
	   	$this->newLoad=true;
	   
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['name'] = array('name'); 
        $this->arrData['volume'] = array('volume','number'); 
        $this->arrData['containertypekey'] = array('selContainerTypeKey'); 
        $this->arrData['partnerid'] = array('partnerID'); 
        $this->arrData['statuskey'] = array('selStatus');  
        $this->arrData['orderlist'] = array('orderList');  
		$this->arrData['teus'] = array('teus', 'number');
       
       	$this->arrLockedTable = array();
        $defaultFieldName = 'itemkey';
        array_push($this->arrLockedTable, array('table'=>'emkl_job_order_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'emkl_job_order_detail_item','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'emkl_order_detail','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'emkl_purchase_order_detail','field'=>$defaultFieldName));
       
           
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'type','title' => 'containerType','dbfield' => 'containertypename','default'=>true,'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'volume','title' => 'volume','dbfield' => 'volume','format'=>'number','default'=>true,'align' => 'right', 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
		$this->overwriteConfig();
	}
	
	 function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
					'.$this->tableContainerType.'.name as containertypename,
					'.$this->tableStatus.'.status as statusname 
				from
					'.$this->tableName.'
                        left join '.$this->tableContainerType.' on  '.$this->tableName.'.containertypekey = '.$this->tableContainerType.'.pkey,
                    '.$this->tableStatus.' 
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ;
          
         return $sql;
    }
     
      
    function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey); 
        
		$name = $arr['name'];  
	 	 
	  	$rs = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['container'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['container'][2]);
		} 
		 
		return $arrayToJs;
	 }
    
    function getContainerType($pkey=''){ 
       
	   $sql = 'select
	   			'.$this->tableContainerType .'.pkey, 
	   			'.$this->tableContainerType .'.name 
              from
			  	'.$this->tableContainerType .' 
			  where
			  	'.$this->tableContainerType .'.statuskey = 1';
                
        if(!empty($pkey))
            $sql .= ' and pkey in ( '.$this->oDbCon->paramString($pkey,',').')';
        
        
       //$sql .=' order by name asc';
         
		return $this->oDbCon->doQuery($sql);
	
   }
        
  
}
?>
