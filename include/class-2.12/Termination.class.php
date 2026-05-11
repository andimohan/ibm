<?php
class Termination extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'termination';
		$this->tableSalesOrder = 'sales_order_subscription_header';
        $this->tableCustomer = 'customer';
        $this->tableMedia = 'media';
        $this->tableEmployee = 'employee';
        $this->tableLocation = 'location';
        $this->tableJobDetails = 'job_details'; 		   
        $this->tableWarehouse = 'warehouse';
        
		$this->tableStatus = 'transaction_status'; 
		$this->securityObject = 'Termination'; 
        $this->isTransaction = true;

        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['terminatedate'] = array('terminateDate','date');
        $this->arrData['salesorderkey'] = array('hidSalesOrderKey');
        $this->arrData['representedkey'] = array('hidRepresentedKey');
        $this->arrData['department'] = array('department');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus');  
         
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 90,  'align' => 'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'terminatedate','title' => 'terminationDate','dbfield' => 'terminatedate','default'=>true, 'width' => 90,  'align' => 'center', 'format' => 'date'));                
        array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'represented','title' => 'representedby','dbfield' => 'representedname','default'=>true, 'width' => 120));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 150));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'media','title' => 'media','dbfield' => 'medianame','default'=>true, 'width' => 80));
        array_push($this->arrDataListAvailableColumn, array('code' => 'sid','title' => 'sid','dbfield' => 'sid','default'=>true, 'width' => 120));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'products','title' => 'products','dbfield' => 'product','default'=>true, 'width' => 100));
//        array_push($this->arrDataListAvailableColumn, array('code' => 'jobDetails','title' => 'jobDetails','dbfield' => 'jobdetailname','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));    
		
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/termination'));

        $this->overwriteConfig();
	   	$this->includeClassDependencies(array( 
			'Warehouse.class.php',  
			'Customer.class.php', 
			'Location.class.php',
			'Employee.class.php', 
			'Media.class.php', 
			'SalesOrderSubscription.class.php'
		)); 
	}
	
	 function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
					'.$this->tableSalesOrder. '.code as refcode,
					'.$this->tableCustomer. '.name as customername,
					'.$this->tableCustomer. '.sid,
                    '.$this->tableEmployee.'.name as representedname,
                    '.$this->tableWarehouse.'.name as warehousename,
					'.$this->tableStatus.'.status as statusname 
				from
					'.$this->tableName.',
					'.$this->tableSalesOrder.'
                    left join '. $this->tableCustomer.' on ' . $this->tableSalesOrder .'.customerkey = ' . $this->tableCustomer .'.pkey ,
                    '.$this->tableWarehouse.',
                    '.$this->tableEmployee.',
                    '.$this->tableStatus.' 
                where
                    '.$this->tableName.'.representedkey = '.$this->tableEmployee.'.pkey and
                    '.$this->tableName.'.salesorderkey = '.$this->tableSalesOrder.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ;
         return $sql;
    }
	
	function afterStatusChanged($rsHeader){
		$rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
		if($rsHeader[0]['statuskey'] == 2)
			$this->changeStatus($rsHeader[0]['pkey'],3);
	}
	
    function validateForm($arr,$pkey = ''){
		$arrayToJs = parent::validateForm($arr,$pkey); 
        $salesOrderSubscription = new SalesOrderSubscription();
		$sokey = $arr['hidSalesOrderKey']; 
		$representedkey = $arr['hidRepresentedKey'];
        if(empty($sokey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderSubscription'][1]);
        else{
            $rsSO = $salesOrderSubscription->getDataRowById($sokey);
            if($rsSO[0]['statuskey']<>3)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderSubscription'][5]);
        }
        
        if(empty($representedkey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['represented'][1]);

		return $arrayToJs;
	 }
     
    function normalizeParameter($arrParam, $trim=false){
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
        return $arrParam;
    }
        
    function validateConfirm($rsHeader){ 
		$salesOrderSubscription = new SalesOrderSubscription();
        $rsSO = $salesOrderSubscription->searchData($salesOrderSubscription->tableName.'.pkey',$rsHeader[0]['salesorderkey'],true,' and '.$salesOrderSubscription->tableName.'.statuskey in(3) ');
        if(empty($rsSO))
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['salesOrderSubscription'][5]);
    } 

    function confirmTrans($rsHeader){
        $customer = new Customer();
        $salesOrderSubscription = new SalesOrderSubscription();
		$rsSO = $salesOrderSubscription->searchData($salesOrderSubscription->tableName.'.pkey',$rsHeader[0]['salesorderkey'],true,' and '.$salesOrderSubscription->tableName.'.statuskey in(3) ');

        $sql = 'update '.$customer->tableName.' set subscriptionstatuskey = 2 where '.$customer->tableName.'.pkey = '.$this->oDbCon->paramString($rsSO[0]['customerkey']).' ';
        $this->oDbCon->execute($sql);  
		$salesOrderSubscription->changeStatus($rsSO[0]['pkey'],5, '', false, true,true);
    }
    
	function cancelTrans($rsHeader,$copy){
        
		$id = $rsHeader[0]['pkey'];
        
        $customer = new Customer();
        $salesOrderSubscription = new SalesOrderSubscription();
		$rsSO = $salesOrderSubscription->searchData($salesOrderSubscription->tableName.'.pkey',$rsHeader[0]['salesorderkey'],true,' and '.$salesOrderSubscription->tableName.'.statuskey in(5) ');
        $sql = 'update '.$customer->tableName.' set subscriptionstatuskey = 1 where '.$customer->tableName.'.pkey = '.$this->oDbCon->paramString($rsSO[0]['customerkey']).' ';
        $this->oDbCon->execute($sql);

		$salesOrderSubscription->changeStatus($rsSO[0]['pkey'],3, '', false, true,true);

        if ($copy)
			$this->copyDataOnCancel($id);	  
    }  

}
?>
