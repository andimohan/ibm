<?php
class PointCashback extends BaseClass{
  
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'point_cashback';   
		$this->tableStatus = 'transaction_status';
		$this->tableCustomer = 'customer'; 
        $this->tableWarehouse = 'warehouse'; 
		$this->securityObject = 'PointCashback'; 
        $this->isTransaction = true;
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
    	$this->arrData['statuskey'] = array('selStatus');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['customerkey'] = array('hidCustomerKey');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['point'] = array('point','number');
        $this->arrData['amount'] = array('amount','number');
        $this->arrData['trdesc'] = array('trDesc');
        
	   	$this->newLoad = true;
	   
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'point','title' => 'point','dbfield' => 'point','default'=>true, 'width' => 80, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
 
//        $this->printMenu = array();
//        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/ar'));

        $this->includeClassDependencies(array(  
                  'Customer.class.php',   
                  'Warehouse.class.php', 
                  'RewardsPoint.class.php', 
                  'SalesOrder.class.php',
         ));  
       
        $this->overwriteConfig();
	}
		
    function getQuery(){
	   
		$sql = '
				select
					'.$this->tableName. '.*,
                  	'.$this->tableCustomer.'.name as customername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename  
				from 
					'.$this->tableName . ',
                    '.$this->tableStatus.' ,
                    '.$this->tableCustomer.' ,
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
		' .$this->criteria ; 
        
        //$sql .=  $this->getWarehouseCriteria() ;
        
        return $sql;
	}

    
    
    function afterUpdateData($arrParam,$action){
        $rewardsPoint = new RewardsPoint();
        $customer = new Customer();
        
        $pkey = $arrParam['pkey'];
        $customerkey = $arrParam['hidCustomerKey'];
		$point = $arrParam['point'];
        $tabletype = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
      
		$arrTable = array();
        $arrTable['pkey'] = $pkey;
        $arrTable['refTableType'] = $tabletype;
        
        $rewardsPoint->deductPoint($customerkey,$point,$arrTable);
    }
    
        
    function validateForm($arr,$pkey = ''){ 
        $customer = new Customer();
                
        $arrayToJs = parent::validateForm($arr,$pkey); 

        $customerkey = $arr['hidCustomerKey'];
        $rsCustomer = $customer->searchDataRow(array($customer->tableName.'.point'),' and '.$customer->tableName.'.pkey = ' . $customer->oDbCon->paramString($customerkey));
        $point = $rsCustomer[0]['point'];
        
        if($point<=0)
            $this->addErrorList($arrayToJs,false,$this->errorMsg['point'][3]); 
		 
        return $arrayToJs;

    
    }
    
    function validateCancel($id,$autoChangeStatus=false){
	 
		$arrayToJs = array();  
		return $arrayToJs;
	 } 	

    function cancelTrans($rsHeader,$copy){   
		
        $rewardsPoint = new RewardsPoint();
 
		$tabletype = $this->getTableKeyAndObj($this->tableName,array('key'))['key'];
      
		$arrTable = array();
        $arrTable['pkey'] = $rsHeader[0]['pkey'];
        $arrTable['refTableType'] = $tabletype; 
 		$rewardsPoint->cancelPointDeduction($rsHeader[0]['customerkey'],$arrTable);
        
		if ($copy)
			$this->copyDataOnCancel($rsHeader[0]['pkey']);	  
        
	}   	  

    function normalizeParameter($arrParam, $trim = false){ 

        $rewardPointValue = $this->loadSetting('rewardsPointUnitValue');  
        $arrParam['amount'] = $arrParam['point'] * $rewardPointValue;

        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
	
	 
    function afterStatusChanged($rsHeader){
         // kalo dr perubahan status
         // harus set ulang cancelreason
         $pkey = $rsHeader[0]['pkey'];
//         $rewardsPoint->resyncCustomerPoint($rsHeader[0]['customerkey']);   
    } 

 
	 
}
		
?>