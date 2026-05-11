<?php  
class TicketSupport extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
        $this->tableName = 'ticket_support_header';
        $this->tableImage = 'ticket_support_image';
        $this->tableUrgency = 'urgency';
        $this->tableDivision = 'employee_category';
        $this->tableCustomer = 'customer'; 
        $this->tableStatus = 'transaction_status';
        $this->tableWarehouse = 'warehouse';
        $this->tableCity = 'city';
        $this->uploadFolder = 'ticket-support/';
        $this->isTransaction = true;
	   
		$this->securityObject = 'TicketSupport'; 
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['trdate'] = array('trDate','date');
        $this->arrData['starttime'] = array('startTime','date');
        $this->arrData['endtime'] = array('endTime','date');
        $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['urgencykey'] = array('selUrgency');  
        $this->arrData['subject'] = array('subject'); 
        $this->arrData['message'] = array('message'); 
	    $this->arrData['divisionkey'] = array('selDivision'); 
        
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'trdate','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align' =>'center', 'format' =>'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename', 'width' => 120));    
        array_push($this->arrDataListAvailableColumn, array('code' => 'subject','title' => 'subject','dbfield' => 'subject','default'=>true,'width' => 250));
        array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
    
		$this->overwriteConfig();
	   
	   	$this->includeClassDependencies(array( 
			'Warehouse.class.php',  
			'Customer.class.php', 
			'City.class.php',
			'Category.class.php', 
			'TicketSupportWorkOrder.class.php', 
			'EmployeeCategory.class.php'
		)); 
   }
	 
	 
	 
    function getQuery(){
	   
	   return '
				select
					'.$this->tableName. '.*,
                    '.$this->tableCustomer.'.name as customername,
                    '.$this->tableCustomer.'.sid,
                    '.$this->tableCustomer.'.phone,
                    '.$this->tableCustomer.'.attention,
                    '.$this->tableCustomer.'.address,
                    '.$this->tableDivision.'.name as divisionname,
                    '.$this->tableUrgency.'.name as urgencyname,
                    '.$this->tableCity.'.name as cityname,
                    '.$this->tableWarehouse.'.name as warehousename,
					'.$this->tableStatus.'.status as statusname
				from 
					'.$this->tableName. ' 
                        left join '.$this->tableDivision.' on  '.$this->tableName.'.divisionkey = '.$this->tableDivision.'.pkey 
                        left join '.$this->tableUrgency.' on  '.$this->tableName.'.urgencykey = '.$this->tableUrgency.'.pkey, 
					'.$this->tableCustomer. ' 
                        left join '. $this->tableCity.' on ' . $this->tableCustomer .'.citykey = ' . $this->tableCity .'.pkey,
					'.$this->tableWarehouse. ' ,
                    '.$this->tableStatus.'
				where  		
					'.$this->tableName . '.customerkey = '.$this->tableCustomer.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey 
 		         ' .$this->criteria ; 
		 
    } 
    
    function afterUpdateData($arrParam, $action){    
        $this->updateImages($arrParam['pkey'], $arrParam['token-item-image-uploader'], $arrParam['item-image-uploader'], $this->tableImage);    
    }

	  function validateForm($arr,$pkey = ''){
		  
		$arrayToJs = parent::validateForm($arr,$pkey); 
       
        $subject = $arr['subject'];
        if(empty($subject)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['ticketSupport'][1]);
        }

        $message = $arr['message'];
        if(empty($message)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['ticketSupport'][2]);
        }
          
        $customer = $arr['hidCustomerKey'];
        if(empty($customer)){
            $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
        }

		 return $arrayToJs;
	 }
    
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
        $ticketSupportWorkOrder = new TicketSupportWorkOrder();

        $rsWO = $ticketSupportWorkOrder->searchData('','',true,' and '.$ticketSupportWorkOrder->tableName.'.ticketkey = '.$this->oDbCon->paramString($id).' and '. $ticketSupportWorkOrder->tableName.'.statuskey in (2,3)');
        if (!empty($rsWO)) 
           $this->addErrorLog( false, '<strong>'.$rsHeader[0]['code'].'</strong> ' .$this->errorMsg[201].'<br><strong>'.$rsWO[0]['code'].'</strong>, ' .$this->errorMsg[225] );
        
      
    }
    
	function cancelTrans($rsHeader,$copy){
		$id = $rsHeader[0]['pkey'];
        $ticketSupportWorkOrder = new TicketSupportWorkOrder();
        
    	$rsTicketWorkOrder = $ticketSupportWorkOrder->searchData('','',true,' and '.$ticketSupportWorkOrder->tableName.'.ticketkey = '.$this->oDbCon->paramString($id).' and '. $ticketSupportWorkOrder->tableName.'.statuskey = 1');
        for($i=0;$i<count($rsTicketWorkOrder);$i++) 
          $ticketSupportWorkOrder->changeStatus($rsTicketWorkOrder[$i]['pkey'],4,'',false,true); 
		 
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
    
    function getUrgency($pkey=''){
        $sql = 'select '.$this->tableUrgency.'.pkey,'.$this->tableUrgency.'.name
				from 
					'.$this->tableUrgency . ','.$this->tableStatus.'
				where  		
					'.$this->tableUrgency . '.statuskey = '.$this->tableStatus.'.pkey
			';

        if(!empty($pkey))
            $sql .= ' AND '.$this->tableUrgency . '.pkey in  ('.$pkey.') ';
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }

    function getDivision($pkey=''){
        $sql = 'select '.$this->tableDivision.'.pkey,'.$this->tableDivision.'.name
				from 
					'.$this->tableDivision . ','.$this->tableStatus.'
				where  		
					'.$this->tableDivision . '.statuskey = '.$this->tableStatus.'.pkey
			';
        
        if(!empty($pkey))
            $sql .= ' AND '.$this->tableDivision . '.pkey in  ('.$pkey.') ';
        
        
        $rs = $this->oDbCon->doQuery($sql);
        return $rs;
    }
     
    function normalizeParameter($arrParam, $trim=false){ 
        
        $arrParam = parent::normalizeParameter($arrParam,true);   
        return $arrParam;
    }
        
    
  }

?>
