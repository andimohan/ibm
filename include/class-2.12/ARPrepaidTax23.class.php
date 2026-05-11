<?php

class ARPrepaidTax23 extends AR{
  
   function __construct(){
		
		parent::__construct();
		
       // yg ada pph 23 hanya jasa 
		$this->tableName = 'ar_prepaid_23';      
        $this->tableSalesInvoice = 'trucking_service_order_invoice_header'; 
        $this->tablePayment = 'ar_prepaid_23_voucher_detail'; // harusnya tidak akan pernah kepake
		$this->tableTax = 'tax';
		$this->securityObject = 'ARPrepaidTax23';
       
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'reference','title' => 'reference','dbfield' => 'refcode','default'=>true, 'width' => 120 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'type','title' => 'type','dbfield' => 'pphtypename',  'width' => 100 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200 ));
        array_push($this->arrDataListAvailableColumn, array('code' => 'amount','title' => 'amount','dbfield' => 'amount','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'outstanding','title' => 'outstanding','dbfield' => 'outstanding','default'=>true, 'width' => 100, 'align' => 'right', 'format' => 'number'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename',  'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'description','title' => 'note','dbfield' => 'trdesc',  'width' => 200));
        
        
        $this->includeClassDependencies(array( 
            'AR.class.php',
            'ARPayment.class.php',
            'ARPrepaidTax23Payment.class.php'
        ));

        $this->overwriteConfig();
       
	}  
    
     function getQuery(){
	   
		$sql = '
				select
					'.$this->tableName. '.*,
                    ('.$this->tableName. '.amountidr - '.$this->tableName. '.tax23outstanding) as tax23balance,
                    if('.$this->tableName. '.statuskey = 1 or '.$this->tableName. '.statuskey = 2, datediff(now(),duedate) , 0)  as datediff,
					'.$this->tableCustomer.'.name as customername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableWarehouse.'.name as warehousename , 
					'.$this->tableCurrency.'.name as currencyname,
					'.$this->tableEmployee.'.name as salesname , 
                    '.$this->tableType .'.name as artypename, 
                    '.$this->tableTax .'.name as pphtypename
				from 
					'.$this->tableName . '
                        left join ' . $this->tableCurrency .' on  '.$this->tableName.'.currencykey = ' . $this->tableCurrency .'.pkey 
                        left join ' . $this->tableEmployee .' on  '.$this->tableName.'.saleskey = ' . $this->tableEmployee .'.pkey 
                        left join ' .  $this->tableType .' on  '.$this->tableName.'.artype = ' . $this->tableType .'.pkey
                        left join ' .  $this->tableTax .' on  '.$this->tableName.'.pphtype = ' . $this->tableTax .'.pkey,
                    '.$this->tableStatus.' ,
                    '.$this->tableCustomer.' ,
                    '.$this->tableWarehouse.' 
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey and 
					'.$this->tableName . '.warehousekey = '.$this->tableWarehouse.'.pkey and 
					'.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
		' .$this->criteria ; 
        
        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCustomerCriteria() ;
          
        return $sql;
	}
    
  
    function getPaymentObj(){
        return  new ARPrepaidTax23Payment();
    }
    
    
}
?>
