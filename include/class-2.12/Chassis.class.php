<?php
class Chassis extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'chassis';
		$this->tableCategory = 'chassis_category'; 
		$this->tableStatus = 'master_status'; 
		$this->securityObject = 'Chassis';  
		
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['kir'] = array('kir');
        $this->arrData['kirexpirydate'] = array('kirExpiryDate','date'); 
        $this->arrData['chassisnumber'] = array('chassisNumber'); 
        $this->arrData['sumbu'] = array('sumbu');   
        $this->arrData['color'] = array('color');   
        $this->arrData['categorykey'] = array('hidCategoryKey');   
        $this->arrData['statuskey'] = array('selStatus');   
        $this->arrData['trdesc'] = array('trDesc');   
        
                            
       	$this->arrLockedTable = array();
        $defaultFieldName = 'chassiskey';
        array_push($this->arrLockedTable, array('table'=>'service_work_order','field'=>$defaultFieldName));
        
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
               
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'chassisNumber','title' => 'chassisNumber','dbfield' => 'chassisnumber','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'kirNumber','title' => 'kirNumber','dbfield' => 'kir','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'kirExpiredDate','title' => 'kirExpiredDate','dbfield' => 'kirexpirydate','default'=>true, 'width' => 150, 'align' =>'center','format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'chassisNumber','title' => 'chassisNumber','dbfield' => 'chassisnumber',  'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
       
        $this->includeClassDependencies(array(
              'Category.class.php',  
              'ChassisCategory.class.php',  
        ));

        $this->overwriteConfig();
	}
	
	 function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
					'.$this->tableCategory. '.name as categoryname,
					'.$this->tableWarehouse. '.name as warehousename,
					'.$this->tableStatus.'.status as statusname
				from
					'.$this->tableName.',
                    '.$this->tableCategory. ',
                    '.$this->tableWarehouse.',
                    '.$this->tableStatus.' 
                where
					'.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
        $sql .=  $this->getWarehouseCriteria() ;
         
         return $sql;
    }
   
     
	
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey);

		$chassisNumber = $arr['chassisNumber'];   
		$kirNumber = $arr['kir'];   

	 	$rs = $this->isValueExisted($pkey,'chassisnumber',$chassisNumber);
		if(empty($chassisNumber)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['chassis'][1]);
		}else if(count($rs) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['chassis'][2]);
		} 
 	  
        if(!empty($kirNumber)){
            $rs = $this->isValueExisted($pkey,'kir',$kirNumber);
            if(count($rs) <> 0){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['chassis'][3]);
            } 
        }
        
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
