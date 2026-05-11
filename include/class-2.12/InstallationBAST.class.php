<?php  
class InstallationBAST extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
        $this->tableName = 'installation_bast';
        $this->tableCustomer = 'customer';
        $this->tableSalesOrderSubs = 'sales_order_subscription_header';
        $this->tableMedia = 'media';
        $this->tableEmployee = 'employee';
        $this->tableLocation = 'location';
        $this->tableWarehouse = 'warehouse';
        $this->tableJobDetails = 'job_details'; 	   
        $this->tableStatus = 'transaction_status';
        $this->isTransaction = true; 

		$this->securityObject = 'InstallationBAST'; 
        

        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['activationdate'] = array('activationDate','date');
        $this->arrData['invoiceduedate'] = array('invoiceDueDate','date');
        $this->arrData['sid'] = array('sid');
        $this->arrData['warehousekey'] = array('selWarehouseKey'); 
        $this->arrData['refkey'] = array('hidSalesOrderSubsKey');
        $this->arrData['employeekey'] = array('hidEmployeeKey');
        $this->arrData['trdesc'] = array('note');   
        $this->arrData['position'] = array('position');   
        $this->arrData['capacity'] = array('capacity');   
        $this->arrData['statuskey'] = array('selStatus');

 
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 90, 'align' =>'center', 'format' =>'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'duedate','title' => 'billingDate','dbfield' => 'invoiceduedate','default'=>true, 'width' => 120, 'align' =>'center', 'format' =>'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'salesordercode','title' => 'salesOrder','dbfield' => 'salesordercode','default'=>true, 'width' => 100,));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customername','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200,));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));

        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printTransaction', 'name' => $this->lang['printTransaction'],  'icon' => 'print', 'url' => 'print/installationBAST'));

		$this->overwriteConfig();
	   	$this->includeClassDependencies(array(
			'SalesOrderSubscription.class.php',  
			'Warehouse.class.php',  
			'Customer.class.php',
			'Employee.class.php', 
			'JobDetails.class.php',
			'Location.class.php', 
			'Media.class.php', 
			'GeneralJournal.class.php', 
		)); 
   }
	 
	 
	 
    function getQuery(){
	   
	   return '
				select
                    '.$this->tableName. '.*,
                    '.$this->tableCustomer. '.name as customername,
                    '.$this->tableCustomer.'.phone, 
                    '.$this->tableCustomer.'.address, 
                    '.$this->tableCustomer.'.attention, 
                    '.$this->tableMedia.'.name as medianame, 
                    '.$this->tableLocation.'.name as locationname, 
                    '.$this->tableSalesOrderSubs.'.code as salesordercode,
                    '.$this->tableSalesOrderSubs.'.product,
                    '.$this->tableJobDetails.'.name as jobdetailname, 
                    '.$this->tableEmployee.'.name as employeename,
                    '.$this->tableStatus.'.status as statusname,
                    '.$this->tableWarehouse.'.name as warehousename
				from 
                    '.$this->tableName. ' ,
                    '.$this->tableCustomer. ' 
                         left join '.$this->tableMedia.' on  '.$this->tableCustomer.'.mediakey = '.$this->tableMedia.'.pkey 
                         left join '.$this->tableLocation.' on '.$this->tableCustomer.'.locationkey = '.$this->tableLocation.'.pkey  ,
                    '.$this->tableSalesOrderSubs.'
                         left join '.$this->tableEmployee.' on  '.$this->tableSalesOrderSubs.'.employeekey = '.$this->tableEmployee.'.pkey
                         left join '.$this->tableJobDetails.' on '.$this->tableSalesOrderSubs.'.jobdetailskey = '.$this->tableJobDetails.'.pkey,  
                    '.$this->tableStatus.',
                    '.$this->tableWarehouse.'  
                where  
                    '.$this->tableName . '.refkey = '.$this->tableSalesOrderSubs.'.pkey and
                    '.$this->tableSalesOrderSubs . '.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableSalesOrderSubs . '.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey
 		         ' .$this->criteria ; 
		 
    }
    
    function afterStatusChanged($rsHeader){
        $rsHeader = $this->getDataRowById($rsHeader[0]['pkey']);
		if ($rsHeader[0]['statuskey'] == 2)
            $this->changeStatus($rsHeader[0]['pkey'],3);
    }

	function validateForm($arr,$pkey = ''){
		$arrayToJs = parent::validateForm($arr,$pkey);
		$salesOrderSubscription = new SalesOrderSubscription(); 
		$sokey = $arr['hidSalesOrderSubsKey']; 
		$invoiceDate = $arr['invoiceDueDate']; 
		if(empty($sokey)) 
            $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderSubscription'][1]);
        else{
            $rsSO = $salesOrderSubscription->getDataRowById($sokey);
            if($rsSO[0]['statuskey']<>2)
                $this->addErrorList($arrayToJs,false,$this->errorMsg['salesOrderSubscription'][2]);
        }
		
		if($invoiceDate==DEFAULT_EMPTY_DATE)
			$this->addErrorList($arrayToJs,false,$this->errorMsg['installationbast'][3]);
		
		return $arrayToJs;
	}

	function validateConfirm($rsHeader){
		$id = $rsHeader[0]['pkey'];
		$salesOrderSubscription = new SalesOrderSubscription(); 
		$invoiceDate = $this->formatDBDate($rsHeader[0]['invoiceduedate'],'d / m / Y');
		 
		$rsSO = $salesOrderSubscription->searchData($salesOrderSubscription->tableName.'.pkey',$rsHeader[0]['refkey'],true,' and '.$salesOrderSubscription->tableName.'.statuskey in(2) ');
		if(empty($rsSO))
		$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['salesOrderSubscription'][2]);
		
		if($invoiceDate == DEFAULT_EMPTY_DATE)
			$this->addErrorLog(false,'<strong>'.$rsHeader[0]['code'].'</strong>. '.$this->errorMsg['installationbast'][3]);

	}
	
	function confirmTrans($rsHeader){
		$customer = new Customer();
        $salesOrderSubscription = new SalesOrderSubscription();
        $rsSales = $salesOrderSubscription->getDataRowById($rsHeader[0]['refkey']);
        $activationDate = $this->formatDBDate($rsHeader[0]['activationdate'],'d / m / Y');
        $invoiceDate = $this->formatDBDate($rsHeader[0]['invoiceduedate'],'d / m / Y');
		$sql = 'update '.$customer->tableName.' set 
                        subscriptionstatuskey = 1,
                        sid = '.$this->oDbCon->paramString($rsHeader[0]['sid']).', 
                        subscriptionactivationdate = '.$this->oDbCon->paramDate($activationDate,' / ').'
               	where 
                        pkey = '.$this->oDbCon->paramString($rsSales[0]['customerkey']);
		
		$this->oDbCon->execute($sql);
		
		$sql = 'update '.$salesOrderSubscription->tableName.' set 
                        invoiceduedate = '.$this->oDbCon->paramDate($invoiceDate,' / ').'
               	where 
                        pkey = '.$this->oDbCon->paramString($rsSales[0]['pkey']);
		$this->oDbCon->execute($sql);
		
		
		$salesOrderSubscription->changeStatus($rsHeader[0]['refkey'],3,'',false,true); 
	} 
	
	function cancelTrans($rsHeader,$copy){
		$id = $rsHeader[0]['pkey'];
		$customer = new Customer();
        $salesOrderSubscription = new SalesOrderSubscription();
        $rsSales = $salesOrderSubscription->getDataRowById($rsHeader[0]['refkey']);
        $emptyDate = DEFAULT_EMPTY_DATE;
		$sql = 'update '.$customer->tableName.' set 
                        subscriptionstatuskey = 2, 
                        sid = \' \',
                        subscriptionactivationdate = '.$this->oDbCon->paramDate($emptyDate).'
                        where 
                        pkey = '.$this->oDbCon->paramString($rsSales[0]['customerkey']);

		$this->oDbCon->execute($sql);
		
		$sql = 'update '.$salesOrderSubscription->tableName.' set 
                        invoiceduedate = '.$this->oDbCon->paramDate($emptyDate).'
               	where 
                        pkey = '.$this->oDbCon->paramString($rsSales[0]['pkey']);
		$this->oDbCon->execute($sql);
		
        if($rsHeader[0]['statuskey'] == 3 || $rsHeader[0]['statuskey'] == 2)
		  $salesOrderSubscription->changeStatus($rsHeader[0]['refkey'],2,'',false,true); 
		
		if ($copy)
			$this->copyDataOnCancel($id);
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
	
	function validateCancel($rsHeader,$autoChangeStatus=false){
		$salesOrderSubscription = new SalesOrderSubscription(); 
 		$rsInvoiced = $salesOrderSubscription->getInvoiceInformation($rsHeader[0]['refkey']);
        if (!empty($rsInvoiced)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsInvoiced[0]['code'].'</strong>, ' .$this->errorMsg[225] );

} 
        
    
  }

?>
