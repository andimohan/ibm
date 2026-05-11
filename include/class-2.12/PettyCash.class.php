<?php  
class PettyCash extends BaseClass{
 
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'petty_cash';
		$this->tableStatus = 'transaction_status';
		$this->tableCar = 'car';
		$this->tableItem = 'item';
		$this->tableCarCategory = 'car_category';
		$this->tableCustomer = 'customer';
		$this->tableLocation = 'location';
		$this->tableSupplier = 'supplier';
	   
		$this->securityObject = 'PettyCash';
		$this->newLoad = true;
       
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey');
        $this->arrData['code'] = array('code'); 
        $this->arrData['trdate'] = array('trDate'); 
        $this->arrData['refkey'] = array('hidRefKey');
        $this->arrData['reftablekey'] = array('reftablekey');
        $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['donumber'] = array('doNumber'); 
        $this->arrData['costkey'] = array('hidCostKey'); 
        $this->arrData['stuffinglocationfromkey'] = array('hidStuffingLocationFromKey');  
        $this->arrData['stuffinglocationkey'] = array('hidStuffingLocationKey');   
        $this->arrData['servicekey'] = array('hidServiceKey');   
        $this->arrData['carkey'] = array('hidCarKey');   
        $this->arrData['driverkey'] = array('hidDriverKey');   
        $this->arrData['codriverkey'] = array('hidCoDriverKey');  
        $this->arrData['qtymulti'] = array('qtyMulti','number'); 
        $this->arrData['debit'] = array('debit','number'); 
        $this->arrData['credit'] = array('credit','number');
        $this->arrData['coakey'] = array('hidCOAKey');   
        $this->arrData['trdesc'] = array('trDesc');   
        $this->arrData['codrivernamedesc'] = array('coDriverNameDesc');   
        $this->arrData['drivernamedesc'] = array('driverNameDesc');   
        $this->arrData['isoutsource'] = array('chkIsOutsource'); 
        $this->arrData['isspk'] = array('chkIsSPK');     
        $this->arrData['supplierkey'] = array('hidSupplierKey');   
        $this->arrData['caroutsource'] = array('carOutsource');
        $this->arrData['checksum'] = array('checkSum');
        $this->arrData['isdownpayment'] = array('chkIsDownpayment');
        $this->arrData['settlementamount'] = array('settlementAmount');       
         
		$this->importUrl = 'import/city';
	   
		$this->arrLockedTable = array();
        $defaultFieldName = 'citykey';
        array_push($this->arrLockedTable, array('table'=>'employee','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'customer','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'supplier','field'=>$defaultFieldName)); 
        array_push($this->arrLockedTable, array('table'=>'depo','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'service_order_header','field'=>$defaultFieldName)); 
        //array_push($this->arrLockedTable, array('table'=>'testimonial','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'utc','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'warehouse','field'=>$defaultFieldName));
               
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true,'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'country','title' => 'country','dbfield' => 'countryname','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
        $this->includeClassDependencies(array(
              'Category.class.php',
              'CityCategory.class.php',
              'GeneralJournal.class.php',
              'Employee.class.php',
			  'Country.class.php'
        ));

		$this->overwriteConfig();
   }
	 
	 
	 
   function getQuery(){
   $sql = 'select
                    ' . $this->tableName . '.*,
                    ' . $this->tableWarehouse .'.name as wareohusename,
                    ' . $this->tableEmployee.'.name as drivername,
                    ' . $this->tableEmployee.'.pkey as driverkey,
                    ' . $this->tableCustomer .'.name as customername,
                    ' . $this->tableCustomer .'.alias as customeralias,
                    ' . $this->tableCar.'.code as policecode ,
                    ' . $this->tableCar.'.pkey as carkey ,
                    ' . $this->tableCar.'.policenumber ,
                    ' . $this->tableLocation.'.name as stuffinglocationname,
					' . $this->tableItem.'.name as servicename,
					' . $this->tableSupplier.'.name as suppliername,
                    locationfrom.name as stuffinglocationfromname,
                    cost.name as costname,
                    codriver.name as codrivername
				from 
                    
					' . $this->tableName . '
                        left join '. $this->tableWarehouse .' on '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey
                        left join '.$this->tableEmployee.' codriver on '.$this->tableName.'.codriverkey = codriver.pkey   
                        left join ' . $this->tableItem . ' on ' . $this->tableName . '.servicekey = ' . $this->tableItem . '.pkey   
                        left join '.$this->tableItem.' cost on '.$this->tableName.'.costkey = cost.pkey   
                        left join ' . $this->tableCar . ' on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey   
                        left join '.$this->tableLocation.' on  '.$this->tableName.'.stuffinglocationkey = '.$this->tableLocation.'.pkey
                        left join '.$this->tableLocation.' locationfrom on '.$this->tableName.'.stuffinglocationfromkey = locationfrom.pkey   
                        left join '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey
                        left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                        left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey,
                    ' . $this->tableStatus . ' 
				where  		          
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey   
                    ' .$this->criteria ; 
        
       
	     $sql .=  $this->getWarehouseCriteria() ;
	   
       return $sql;
    }  
 
    function afterUpdateData($arrParam, $action){ 

    }

 function editData($arrParam){ 
 
        $arrayToJs = array();

        try { 
			if(!$this->oDbCon->startTrans(true))
				throw new Exception($this->errorMsg[100]);

            //$arrParam = $this->normalizeParameter($arrParam);
            $arrParam['checkSum'] = $this->convertDataToMD5($arrParam);
            $arrayToJs = $this->validateForm($arrParam, $arrParam['hidId']);

            if (!empty($arrayToJs))  return $arrayToJs;
            
            $sql = 'update 
                    '.$this->tableName.' 
                set
                    customerkey = '.$this->oDbCon->paramString($arrParam['hidCustomerKey']).',
                    trdate = '.$this->oDbCon->paramString($arrParam['trDate']).',
                    costkey = '.$this->oDbCon->paramString($arrParam['hidCostKey']).',
                    stuffinglocationfromkey = '.$this->oDbCon->paramString($arrParam['hidStuffingLocationFromKey']).',
                    stuffinglocationkey = '.$this->oDbCon->paramString($arrParam['hidStuffingLocationKey']).',
                    servicekey = '.$this->oDbCon->paramString($arrParam['hidServiceKey']).',
                    carkey = '.$this->oDbCon->paramString($arrParam['hidCarKey']).',
                    driverkey = '.$this->oDbCon->paramString($arrParam['hidDriverKey']).',
                    codriverkey = '.$this->oDbCon->paramString($arrParam['hidCoDriverKey']).',
                    qtymulti = '.$this->oDbCon->paramString($arrParam['qtyMulti']).',
                    donumber = '.$this->oDbCon->paramString($arrParam['doNumber']).',
                    debit = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['debit'])).',
                    credit = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['credit'])).',
                    trdesc = '.$this->oDbCon->paramString($arrParam['trDesc']).',
                    drivernamedesc = '.$this->oDbCon->paramString($arrParam['driverNameDesc']).',
                    codrivernamedesc = '.$this->oDbCon->paramString($arrParam['coDriverNameDesc']).',
                    supplierkey = '.$this->oDbCon->paramString($arrParam['hidSupplierKey']).',
                    caroutsource = '.$this->oDbCon->paramString($arrParam['carOutsource']).',
                    isoutsource = '.$this->oDbCon->paramString($arrParam['chkIsOutsource']).',
                    isspk = '.$this->oDbCon->paramString($arrParam['chkIsSPK']).',
                    checksum = '.$this->oDbCon->paramString($arrParam['checkSum']).',
                    isdownpayment = '.$this->oDbCon->paramString($arrParam['chkIsDownpayment']).',
                    settlementamount = '.$this->oDbCon->paramString($this->unFormatNumber($arrParam['settlementAmount'])).'
                where 
                    pkey = '.$this->oDbCon->paramString($arrParam['hidId']);
                    
                    $this->oDbCon->execute($sql);	

         
         $this->oDbCon->endTrans();

        } catch (Exception $e) {

            $this->oDbCon->rollback();
        }

        
        // $this->oDbCon->execute($sql);
        // $this->oDbCon->doQuery($sql);

        return  $arrayToJs;
    }

   
    function getDataPettyCash($criteria = '', $order = '') {
        $sql = 'select
                    ' . $this->tableName . '.*,
                    DATE_FORMAT(' . $this->tableName . '.trdate, "%d / %m / %Y") AS trdate,
                    ' . $this->tableWarehouse .'.name as wareohusename,
                    ' . $this->tableEmployee.'.name as drivername,
                    ' . $this->tableEmployee.'.pkey as driverkey,
                    ' . $this->tableCustomer .'.name as customername,
                    ' . $this->tableCustomer .'.alias as customeralias,
                    ' . $this->tableCar.'.code as policecode ,
                    ' . $this->tableCar.'.pkey as carkey ,
                    ' . $this->tableCar.'.policenumber ,
                    ' . $this->tableLocation.'.name as stuffinglocationname,
					' . $this->tableItem.'.name as servicename,
					' . $this->tableSupplier.'.name as suppliername,
                    locationfrom.name as stuffinglocationfromname,
                    cost.name as costname,
                    codriver.name as codrivername
				from 
                    
					' . $this->tableName . '
                        left join '. $this->tableWarehouse .' on '. $this->tableName .'.warehousekey = '. $this->tableWarehouse .'.pkey
                        left join '.$this->tableEmployee.' codriver on '.$this->tableName.'.codriverkey = codriver.pkey   
                        left join ' . $this->tableItem . ' on ' . $this->tableName . '.servicekey = ' . $this->tableItem . '.pkey   
                        left join '.$this->tableItem.' cost on '.$this->tableName.'.costkey = cost.pkey   
                        left join ' . $this->tableCar . ' on ' . $this->tableName . '.carkey = ' . $this->tableCar . '.pkey   
                        left join '.$this->tableLocation.' on  '.$this->tableName.'.stuffinglocationkey = '.$this->tableLocation.'.pkey
                        left join '.$this->tableLocation.' locationfrom on '.$this->tableName.'.stuffinglocationfromkey = locationfrom.pkey   
                        left join '.$this->tableEmployee.' on '.$this->tableName.'.driverkey = '.$this->tableEmployee.'.pkey
                        left join '.$this->tableCustomer.' on '.$this->tableName.'.customerkey = '.$this->tableCustomer.'.pkey
                        left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey = '.$this->tableSupplier.'.pkey,
                    ' . $this->tableStatus . ' 
				where  		          
					' . $this->tableName . '.statuskey = ' . $this->tableStatus . '.pkey   
			';

        if (!empty($criteria)){
            $sql .= $criteria;
        }
		
		
		
	    $sql .=  $this->getWarehouseCriteria() ;
		
        if (!empty($order))
            $sql .= $order;
        

        $rs = $this->oDbCon->doQuery($sql);

        return $rs;
    }
 
	
     
     
    function validateForm($arr,$pkey = ''){
	
		$arrayToJs = parent::validateForm($arr,$pkey); 
	
        $checkSum = $arr['checkSum'];
        $criteria = '';
        if ($arr['trDate'] == '70-01-01') {
            $this->addErrorList($arrayToJs,false,$this->errorMsg['date'][1]);
        }
        
        if(!empty($pkey) || $pkey != '') {
            $criteria = ' and ' .  $this->tableName.'.pkey <> '.$this->oDbCon->paramString($pkey).' ';
        }
        
        
        // natni direview lg, karena ad pencatatan 0 utk supplier
        //$debit = $this->unFormatNumber($arr['debit']);
        //$credit = $this->unFormatNumber($arr['credit']);

        //if(($debit == 0) && ($credit == 0)) {
        //    $this->addErrorList($arrayToJs,false,$this->lang['debit'].' - '.$this->lang['credit'].' '.$this->errorMsg[512]);
        //}

        //$checkData = $this->searchData('','',true, ' and not ' . $this->tableName.'.checksum  is null and '.$this->tableName.'.checksum <> '.$this->oDbCon->paramString('').' and ' . $this->tableName.'.checksum = '.$this->oDbCon->paramString($checkSum).' '.$criteria.' ');      
        //if(!empty($checkData)) {
        //    $this->addErrorList($arrayToJs,false,$this->formatDBDate($checkData[0]['trdate'], 'd / m / Y') . ' '. $checkData[0]['customername'].'  '. $checkData[0]['donumber'] .'  '. $checkData[0]['servicename'] . ' ' . $checkData[0]['stuffinglocationname'] . ' - ' . $this->lang['duplicateData']); 
        //}

        
		return $arrayToJs;
        
    }


    function convertDataToMD5($arrParam) 
    {
        unset(
            $arrParam['hidId'], 
            $arrParam['code'],
            $arrParam['trDate'],
            $arrParam['trDesc'],
            $arrParam['checkSum']
        );

        $arrParam['credit'] = $this->unFormatNumber($arrParam['credit']);
        $arrParam['debit'] = $this->unFormatNumber($arrParam['debit']);
        

        return md5(json_encode($arrParam));
    }
 
          
    function normalizeParameter($arrParam, $trim = false){  
        
        $arrParam['checkSum'] = $this->convertDataToMD5($arrParam);
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
        return $arrParam; 
    }  


    function sumAccount($coakey, $startDate='', $endDate='', $groupByDate = false){
		  
        // fungsi ini untuk ambil saldo tgl sebelumnya
        
        // kedepannya coba ambil dr closingkan masing2 coa amount kalo sudah ada
        
		$criteria = '';
	 
		if (!empty($endDate))  $criteria .= ' and trdate < '.$this->oDbCon->paramDate($endDate,' / '); // jgn pake 23:59:59 karena buat ambil saldo tgl sebelumnya
		if (!empty($startDate))  $criteria .= ' and trdate >= '.$this->oDbCon->paramDate($startDate,' / '); 
		  
		$sql = 'select 
                  DATE_FORMAT(trdate, "%c-%Y") as dateindex, 
                  coalesce(sum(debit-credit-settlementamount),0) as balance,
                  coalesce(sum(debit),0) as debit,
                  coalesce(sum(credit),0) as credit,
                  coakey
               from
                   '.$this->tableName.'
               where 
                    coakey in ('.$this->oDbCon->paramString($coakey,',').')'. $criteria.'
                    ';		 
        
        $arrGroup = array();
        
        if(is_array($coakey)) array_push($arrGroup,'coakey'); 
        if($groupByDate) array_push($arrGroup,'year(trdate), month(trdate)'); 
        
        if(!empty($arrGroup)) 
            $sql .= ' group by ' . implode(',',$arrGroup);
		
		$rs =  $this->oDbCon->doQuery($sql);
        
        
        return (!is_array($coakey)) ? $rs[0] : $this->reindexDetailCollections($rs,'coakey');  
	}
    function getDataDashboardSummary($coakey, $endPeriod)
    {
        $endPeriod = date("Y-m-t 23:59:59", strtotime($endPeriod));
        $sql = 'select  
                    max(trdate) as lasttrdate,
                    coalesce(sum(debit-credit-settlementamount),0) as balance,
                    coalesce(sum(debit),0) as debit,
                    coalesce(sum(credit),0) as credit,
                    coalesce(sum(settlementamount),0) as settlement,
                    coakey
                from
                    ' . $this->tableName . '
                where 
                    coakey in (' . $this->oDbCon->paramString($coakey, ',') . ')
                    and trdate <= ' . $this->oDbCon->paramString($endPeriod) . '
                    group by coakey
                ';
                
        $rs =  $this->oDbCon->doQuery($sql);

        return (!is_array($coakey)) ? $rs[0] : $this->reindexDetailCollections($rs, 'coakey');
    }
    
    
    
  }

?>
