<?php
  
class RentalTimeSheet extends BaseClass{ 
    
    function __construct(){

            parent::__construct();

            $this->tableName = 'rental_time_sheet_header';
            $this->tableNameDetail = 'rental_time_sheet_detail';
            $this->tableSO = 'sales_order_rental_header';
            $this->tableSODetail = 'sales_order_rental_detail';
            $this->tableCustomer = 'customer';
            $this->tableCar = 'car';
            $this->tableCity = 'city';
            $this->tableEmployee = 'employee';
            $this->tableWarehouse = 'warehouse'; 
            $this->tableStatus = 'transaction_status';
            $this->tableHistory = 'history'; 
            $this->tableLocation = 'location'; 
            $this->tableItem = 'item'; 		
            $this->tableItemUnit = 'item_unit'; 	
            $this->tableTimeUnit = 'time_unit';	
            $this->tableCartTemp = 'cart_temp';  
            $this->isTransaction = true; 		

            $this->autoPrintURL = 'print/salesRentalQuotation';
         
            $this->securityObject = 'SalesRentalQuotation';   

            $this->arrLinkedTable = array(); 
            $defaultFieldName = 'refkey';
            array_push($this->arrLinkedTable, array('table'=>'sales_delivery_header','field'=>$defaultFieldName));  
            array_push($this->arrLinkedTable, array('table'=>'ar','field'=>$defaultFieldName));

            $this->arrDataDetail = array();  
            $this->arrDataDetail['pkey'] = array('hidDetailKey');
            $this->arrDataDetail['refkey'] = array('pkey','ref');
            $this->arrDataDetail['worktime'] = array('workTime','number');
            $this->arrDataDetail['workhour'] = array('workHour','number');
            $this->arrDataDetail['overtime'] = array('overTime','number');
			$this->arrDataDetail['startdate'] = array('trStartDate','date');
			$this->arrDataDetail['startdate2'] = array('trStartDate2','date');
			$this->arrDataDetail['restdate'] = array('trRestDate','date');
			$this->arrDataDetail['restdate2'] = array('trRestDate2','date');
			$this->arrDataDetail['endate'] = array('trEndDate','date');
 
            $arrDetails = array();
            array_push($arrDetails, array('dataset' => $this->arrDataDetail)); 

            $this->arrData = array(); 
            $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails)); 
            $this->arrData['code'] = array('code');
            $this->arrData['name'] = array('name');
            $this->arrData['customcodekey'] = array('selCustomCode');  
            $this->arrData['trdate'] = array('trDate','date');
            $this->arrData['warehousekey'] = array('selWarehouseKey');
            $this->arrData['customerkey'] = array('hidRecipientKey'); 
			$this->arrData['refkey'] = array('hidRefkey');
			$this->arrData['carkey'] = array('hidCarKey');
			$this->arrData['employeekey'] = array('hidEmployeeKey');
			$this->arrData['refsodetailkey'] = array('selJODetailKey');
			$this->arrData['trdesc'] = array('trDesc'); 
			$this->arrData['statuskey'] = array('selStatus'); 
          
            $this->arrDataListAvailableColumn = array(); 
            array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'trdate','default'=>true, 'width' => 100, 'align'=>'center', 'format' => 'date'));
            array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
            array_push($this->arrDataListAvailableColumn, array('code' => 'customer','title' => 'customer','dbfield' => 'customername','default'=>true, 'width' => 200));
        	array_push($this->arrDataListAvailableColumn, array('code' => 'refcode','title' => 'refCode','dbfield' => 'refcode','default'=>true, 'width' => 100));    
			array_push($this->arrDataListAvailableColumn, array('code' => 'employee','title' => 'employee','dbfield' => 'employeename','default'=>true,  'width' => 150));
            array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
            array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
                            	
			$this->includeClassDependencies(array( 
                   'Car.class.php',    
                   'Warehouse.class.php',    
                   'City.class.php', 
                   'Customer.class.php', 
                   'Item.class.php',  
                   'ItemUnit.class.php',
                   'TimeUnit.class.php',
                   'Location.class.php',
                   'CustomCode.class.php',
                   'GeneralJournal.class.php',
                   'SalesOrderRental.class.php',
                   'Employee.class.php' 
            ));
		
            $this->printMenu = array();
            array_push($this->printMenu,array('code' => 'printInvoice', 'name' => $this->lang['print']  ,  'icon' => 'print', 'url' => 'print/salesRentalQuotation'));
  
            array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
        
    }
 
            
    
    function getQuery(){

        $sql = '
            SELECT '.$this->tableName.'.* ,
               '.$this->tableCustomer.'.name as customername,
               '.$this->tableLocation.'.name as locationname ,
               '.$this->tableWarehouse.'.name as warehousename,
               '.$this->tableStatus.'.status as statusname ,
               '.$this->tableEmployee.'.name as employeename, 
			   '.$this->tableCar.'.code as policecode ,
			   '.$this->tableCar.'.policenumber , 
               '.$this->tableSO.'.code as refcode 
            FROM 
                '.$this->tableStatus.', 
                '.$this->tableSO.' 
				left join '.$this->tableCustomer.' on  '.$this->tableSO.'.customerkey = '.$this->tableCustomer.'.pkey
				left join '.$this->tableCity.' on  '.$this->tableCustomer.'.citykey = '.$this->tableCity.'.pkey,
                '.$this->tableWarehouse.',
                '.$this->tableName.' 
			         left join '.$this->tableCar.' on   '.$this->tableName.'.carkey = '.$this->tableCar.'.pkey 
			         left join '.$this->tableEmployee.' on   '.$this->tableName.'.employeekey = '.$this->tableEmployee.'.pkey 
                     left join '.$this->tableLocation.' on '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey 
            WHERE '.$this->tableName.'.refkey = '.$this->tableSO.'.pkey and
                     '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                     '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
        ' .$this->criteria ; 


        $sql .=  $this->getWarehouseCriteria() ;
        $sql .=  $this->getCompanyCriteria() ;

        return $sql;
    }  
   

    function validateForm($arr,$pkey = ''){
            $item = new Item();   

            $arrayToJs = parent::validateForm($arr,$pkey); 

            $customerkey = $arr['hidRecipientKey'];  
            $arrStartDate = $arr['trStartDate']; 


            //validasi kalo status gk menunggu gk bisa edit 
            if (!empty($pkey)){
                $rs = $this->getDataRowById($pkey);
                if ($rs[0]['statuskey'] <> 1){
                    $this->addErrorList($arrayToJs,false,$this->errorMsg[212]);
                }
            }  

            if(empty($customerkey)){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['customer'][1]);
            } 

            if(empty($arrStartDate)) 
                 $this->addErrorList($arrayToJs,false,  $this->errorMsg[501]);  


            $arrDetailKeys = array(); 

  
            return $arrayToJs;
    }
 

    function validateConfirm($rsHeader){
        $id = $rsHeader[0]['pkey'];  
    }


    function confirmTrans($rsHeader){ 

    } 
       
    function validateCancel($rsHeader,$autoChangeStatus=false){ 
        $id = $rsHeader[0]['pkey'];
   
     } 



   function cancelTrans($rsHeader,$copy){ 
        $id = $rsHeader[0]['pkey']; 
       
        if ($copy)
            $this->copyDataOnCancel($id);	  

//        $this->cancelGLByRefkey($rsHeader[0]['pkey'],$this->tableName);

    }  
   

    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
      $sql = 'select
            '.$this->tableNameDetail.'.*,
            '.$this->tableItem.'.name as itemname,
            '.$this->tableItem.'.code as itemcode,
            '.$this->tableTimeUnit.'.name as timename,
            '.$this->tableItemUnit.'.name as unitname,
            baseunit.name as baseunitname
        from
            '.$this->tableNameDetail.',
            '.$this->tableItem.',
            '.$this->tableTimeUnit.',
            '.$this->tableItemUnit.',
            '.$this->tableItemUnit.' baseunit
        where  
            '.$this->tableNameDetail .'.itemkey = '.$this->tableItem.'.pkey and
            '.$this->tableNameDetail.'.unitkey = '.$this->tableItemUnit.'.pkey and
            '.$this->tableNameDetail.'.timeunitkey = '.$this->tableTimeUnit.'.pkey and
            '.$this->tableItem.'.baseunitkey = baseunit.pkey and
            '. $this->tableNameDetail.'.refkey in  ('.$this->oDbCon->paramString($pkey,',') . ') ' ;

        $sql .= $criteria;
  
        return $this->oDbCon->doQuery($sql);

    }
   
        
     function normalizeParameter($arrParam, $trim = false){ 
          
            $arrItemkey = $arrParam['hidItemKey'];
            $arrQty = $arrParam['qty']; 
            $arrPriceinunit = $arrParam['priceInUnit'];   
            $arrTotalDays = $arrParam['totalDays'];   
            $arrUnitKey = $arrParam['selUnit']; 

 
            $arrParam = parent::normalizeParameter($arrParam,true); 

            
        return $arrParam;
    }
     
}
?>
