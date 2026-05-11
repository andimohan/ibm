<?php
class Car extends BaseClass{
    
   function __construct(){
		
		parent::__construct();
		
		$this->tableName = 'car';
		$this->tableSparePartIntervalDetail = 'car_sparepart_type_interval_detail';
		$this->tableBrand = 'brand';
		$this->tableSupplier = 'supplier';
        $this->tableSeries = 'car_series';
		$this->tableCategory = 'car_category';
		$this->tableFuelType = '_fuel_type'; 
        $this->tableWarehouse = 'warehouse';
        $this->tableEmployee = 'employee';
		$this->tableGPS = 'gps'; 
		$this->tableStatus = 'master_status'; 
        $this->tableTruckingServiceWorkOrder = 'trucking_service_work_order';
        $this->tableTruckingServiceJobOrder = 'trucking_service_order_header';
        $this->tableCustomer = 'customer';
        $this->tableConsignee = 'consignee';
		$this->tablePartnershipType = 'vehicle_partnership_type'; 
		$this->tableMileage = 'vehicle_mileage'; 
        $this->tableItemPosition = 'item_position';
        $this->tableItem = 'item';
        $this->tableSparePartType = 'sparepart_type';
        $this->tableCarItemDetail = 'car_item_detail';
        $this->tableMaintenance = 'car_service_maintenance_header';
		$this->tableMaintenanceDetail = 'car_service_maintenance_detail';
        $this->securityObject = 'Car'; 
       
       $this->activeModule = $this->isActiveModule(array('CarServiceMaintenance'));

       
        $arrDetails = array(); 
        if($this->activeModule['carservicemaintenance']){
            $this->arrSparePartIntervalDetail = array();
            $this->arrSparePartIntervalDetail['pkey'] = array('hidDetailSparePartIntervalKey');
            $this->arrSparePartIntervalDetail['refkey'] = array('pkey','ref'); 
            $this->arrSparePartIntervalDetail['spareparttypekey'] = array('hidSparepartTypeKey');
            $this->arrSparePartIntervalDetail['mileage'] = array('mileage','number'); 
            $this->arrSparePartIntervalDetail['month'] = array('month','number'); 

            array_push($arrDetails, array('dataset' => $this->arrSparePartIntervalDetail, 'tableName' => $this->tableSparePartIntervalDetail)); 
        }
       
        $this->arrData = array(); 
        $this->arrData['pkey'] = array('pkey', array('dataDetail' =>  $arrDetails));
        $this->arrData['code'] = array('code');
        $this->arrData['brandkey'] = array('hidBrandKey');
        $this->arrData['warehousekey'] = array('selWarehouse');
        $this->arrData['categorykey'] = array('hidCategoryKey'); 
        $this->arrData['bpkbname'] = array('bpkbName');
        $this->arrData['bpkbnumber'] = array('bpkbNumber');
        $this->arrData['year'] = array('year');
        $this->arrData['policenumber'] = array('policeNumber');
        $this->arrData['licensenumber'] = array('licenseNumber');
        $this->arrData['licenseexpirydate'] = array('licenseExpiryDate','date');
        $this->arrData['licensetaxexpirydate'] = array('licenseTaxExpiryDate','date');
        $this->arrData['kir'] = array('kir');
        $this->arrData['kirexpirydate'] = array('kirExpiryDate','date');
        $this->arrData['machinenumber'] = array('machineNumber');
        $this->arrData['chassisnumber'] = array('chassisNumber');
        $this->arrData['tid'] = array('tid');
        $this->arrData['tidexpirydate'] = array('tidExpiryDate','date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['isassets'] = array('isassets');
        $this->arrData['serieskey'] = array('hidCarSeriesKey');
        $this->arrData['capacity'] = array('capacity','number');
        $this->arrData['fueltype'] = array('fuelType'); 
        $this->arrData['statuskey'] = array('selStatus'); 
        $this->arrData['customerkey'] = array('hidCustomerKey'); 
        $this->arrData['gpstrackerid'] = array('gpsTrackerId'); 
        $this->arrData['commissiontype'] = array('selCommissionType');
        $this->arrData['commission'] = array('commissionValue','number');
        $this->arrData['adminfee'] = array('adminFee','number');
        $this->arrData['supplierkey'] = array('hidSupplierKey');
        $this->arrData['isvendorkey'] = array('chkIsVendor');
        $this->arrData['vendorpartnershiptype'] = array('selCarContract');
        $this->arrData['driverkey'] = array('hidDriverKey');
        $this->arrData['cbm'] = array('cbm');
        $this->arrData['length'] = array('length');
        $this->arrData['width'] = array('width');
        $this->arrData['height'] = array('height');
        $this->arrData['gpskey'] = array('selGPSKey');
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code', 'title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'carRegistrationNumber','title' => 'carRegistrationNumber','dbfield' => 'policenumber','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'series','title' => 'series','dbfield' => 'seriesname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'brand','title' => 'brand','dbfield' => 'brandname','default'=>true, 'width' => 120));
        array_push($this->arrDataListAvailableColumn, array('code' => 'category','title' => 'category','dbfield' => 'categoryname','width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'bpkbRegisteredNumber','title' => 'bpkbRegisteredNumber','dbfield' => 'bpkbnumber','width' => 100)); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'year','title' => 'year','dbfield' => 'year', 'align' => 'center', 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername', 'width' => 150));
       
 
       	$this->arrLockedTable = array();
        $defaultFieldName = 'carkey';
        array_push($this->arrLockedTable, array('table'=>'sales_order_car_service_header','field'=>$defaultFieldName));
        array_push($this->arrLockedTable, array('table'=>'service_work_order','field'=>$defaultFieldName)); 
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
              
        $this->printMenu = array();
        array_push($this->printMenu,array('code' => 'printQR', 'name' => $this->lang['printQRCode'],  'icon' => 'print', 'url' => 'print/carQR'));
        array_push($this->printMenu,array('code' => 'carSpareparts', 'name' => $this->lang['print'] .  ' ' . $this->lang['carSpareparts'],  'icon' => 'print', 'url' => 'print/carSpareparts'));
     
        $this->newLoad = true;
        $this->importUrl = 'import/car';
       
       
        $this->includeClassDependencies(array(
              'Brand.class.php',  
              'Category.class.php',  
              'CarSeries.class.php',  
              'CarCategory.class.php',  
              'Employee.class.php',  
              'Supplier.class.php',  
              'Warehouse.class.php',  
              'GPS.class.php',
              'GPSConnection.class.php',
              'CarServiceMaintenance.class.php',
              'Item.class.php'
        ));
       
		$this->overwriteConfig();
	}
	
 	function getQuery(){
	   
	   $sql = '
			select
					'.$this->tableName. '.*,
                    concat('.$this->tableName. '.code,\' - \', '.$this->tableName. '.policenumber) as codepolicenumber,
					'.$this->tableSupplier. '.name as suppliername,
					'.$this->tableBrand. '.name as brandname,
                    '.$this->tableSeries. '.name as seriesname,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableCategory. '.name as categoryname,
					'.$this->tableWarehouse. '.name as warehousename,
                    '.$this->tableFuelType. '.name as fueltype,
					'.$this->tableGPS. '.name as gpsprovidername,
					'.$this->tableEmployee. '.name as drivername 
				from
					'.$this->tableName.' 
                    left join '.$this->tableSupplier. ' on '.$this->tableName. '.supplierkey = '.$this->tableSupplier. '.pkey 
                    left join '.$this->tableBrand. ' on '.$this->tableName. '.brandkey = '.$this->tableBrand. '.pkey 
                    left join '.$this->tableSeries. ' on '.$this->tableName. '.serieskey = '.$this->tableSeries. '.pkey
                    left join '.$this->tableFuelType. ' on '.$this->tableName. '.fueltype = '.$this->tableFuelType. '.pkey
					left join '.$this->tableGPS.' on '.$this->tableName. '.gpskey = '.$this->tableGPS. '.pkey
              	    left join '.$this->tableEmployee.' on '.$this->tableName. '.driverkey = '.$this->tableEmployee. '.pkey, 
                    '.$this->tableStatus.',
                    '.$this->tableWarehouse.',
                    '.$this->tableCategory.' 
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
					'.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey
 		' .$this->criteria ; 
         
         
         $sql .=  $this->getWarehouseCriteria() ;
		  
         return $sql;
    }
    
	
	function validateForm($arr,$pkey = ''){
		   
		$arrayToJs = parent::validateForm($arr,$pkey);

        $carSeries = new CarSeries();     
        
		$policeNumber = $arr['policeNumber'];   
		$licenseNumber = $arr['licenseNumber'];   
		$kirNumber = $arr['kir'];   
		$bpkbNumber = $arr['bpkbNumber'];   
		$machineNumber = $arr['machineNumber'];  
		$chassisNumber = $arr['chassisNumber'];    
        //$series = $arr['hidCarSeriesKey'];
        $brand = $arr['hidBrandKey'];   
        $isVendor = $arr['chkIsVendor']; 
        $supplierkey = $arr['hidSupplierKey']; 
  
        if($this->checkTotalItemLimitation($this->tableName,PLAN_TYPE['maxvehicle'],$pkey)){  
          $this->addErrorList($arrayToJs,false,$this->errorMsg['limit'][1]);  
        }
        
        $rs = $this->isValueExisted($pkey,'policenumber',$policeNumber);
		if(empty($policeNumber)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['car'][1]);
		}else if(count($rs) <> 0){ 
			$this->addErrorList($arrayToJs,false,$this->errorMsg['car'][2]);
		}  
        
        // utk trucking, seri mobil gk wajib diisi 
       /* if(!empty($series)){ 
            $rsSeries = $carSeries->getDataRowById($series); 
            if(empty($rsSeries))
                $this->addErrorList($arrayToJs,false, $this->errorMsg['series'][1]);

            if ($rsSeries[0]['brandkey'] <> $brand) 
                 $this->addErrorList($arrayToJs,false, $this->errorMsg['series'][3]);  
        }*/
        
        if($isVendor && empty($supplierkey))
            $this->addErrorList($arrayToJs,false, $this->errorMsg['supplier'][1]);
        

        /*
        if (!empty($licenseNumber)){
            $rs = $this->isValueExisted($pkey,'licensenumber',$licenseNumber);
            if(count($rs) <> 0){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][3]);
            }  
        }
	 	
        
        if (!empty($bpkbNumber)){ 
            $rs = $this->isValueExisted($pkey,'bpkbNumber',$bpkbNumber);
            if(count($rs) <> 0){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][7]);
            } 
        } 
        
        
        if (!empty($kirNumber)){ 
            $rs = $this->isValueExisted($pkey,'kir',$kirNumber);
            if(count($rs) <> 0){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][4]);
            } 
        } 
        
        if (!empty($machineNumber)){  
            $rs = $this->isValueExisted($pkey,'machinenumber',$machineNumber);
            if(count($rs) <> 0){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][5]);
            } 
        }
        
        if (!empty($chassisNumber)){ 
            $rs = $this->isValueExisted($pkey,'chassisnumber',$chassisNumber);
            if(count($rs) <> 0){
                $this->addErrorList($arrayToJs,false,$this->errorMsg['car'][6]);
            } 
        }
        
	*/
	   return $arrayToJs;
	 }	 
	  
    function changeStatus($id, $newStatus, $reason='',$copy=false, $autoChangeStatus = false, $ignoreValidation = false){ 
    
        if(PLAN_TYPE['maxvehicle'] > -1){ 
            $arrayToJs =  array();  
            $rs = $this->searchDataRow(array($this->tableName.'.code'), ' and '.$this->tableName.'.pkey = ' . $this->oDbCon->paramString($id));
			$this->addErrorList($arrayToJs,false,'<strong>'.$rs[0]['code'].'</strong>. ' .$this->errorMsg[201]); 
          	return $arrayToJs;
        }
            
        parent::changeStatus($id, $newStatus, $reason ,$copy , $autoChangeStatus , $ignoreValidation );
        
    }
 
    
    
    function generateDefaultQueryForAutoComplete($returnField){ 
            $sql = 'select
					'.$returnField['key'].',
					'.$returnField['value'].' as value , 
                    '.$this->tableBrand. '.name as brandname,
					'.$this->tableCategory. '.name as categoryname,
					'.$this->tableEmployee. '.pkey as driverkey,
					'.$this->tableEmployee. '.name as drivername,
					'.$this->tableName. '.year
				from 
				    '.$this->tableName . ' 
                        left join  '.$this->tableBrand. ' on  '.$this->tableBrand. '.pkey = '.$this->tableName . '.brandkey
                        left join  '.$this->tableEmployee. ' on  '.$this->tableEmployee. '.pkey = '.$this->tableName . '.driverkey,
                    '.$this->tableStatus.',
                    '.$this->tableCategory. '
				where  		
					'.$this->tableName . '.statuskey = '.$this->tableStatus.'.pkey  and
					'.$this->tableName . '.categorykey = '.$this->tableCategory.'.pkey  
			'; 
        return $sql;
        
    } 
    
 
    function normalizePoliceNumber($code){
        
        // ilangin semua spasi dulu
        
        $code = str_replace(' ','',$code);
        $array = str_split($code);    
         
        $arrResult = array();
        $temp = '';    
        $isNumeric = false;
        
        for($i=0;$i<count($array);$i++){  
            
            if ($isNumeric != is_numeric($array[$i])){
                array_push($arrResult,$temp);
                $temp = ''; 
                $isNumeric = is_numeric($array[$i]);
            }
            
            $temp .= strtoupper($array[$i]);
            
        }
        
        if (!empty($temp)){
            array_push($arrResult,$temp);
        }
        
        $code = implode(' ', $arrResult);
        return trim($code);
    }
       
    function getVehicleAvailabilityReport($criteria='',$order='',$pkey=''){
        $sql = '
			select
					'.$this->tableName. '.*,
					'.$this->tableSupplier. '.name as suppliername,
					'.$this->tableStatus.'.status as statusname,
					'.$this->tableCategory. '.name as categoryname,
					'.$this->tableTruckingServiceJobOrder. '.code as jocode,
					'.$this->tableTruckingServiceWorkOrder. '.code as wocode,
					'.$this->tableTruckingServiceWorkOrder. '.trdate as wodate,
					concat('.$this->tableTruckingServiceWorkOrder. '.routefrom, \' - \','.$this->tableTruckingServiceWorkOrder. '.routeto)  as route,
					'.$this->tableWarehouse. '.name as warehousename,
                    '.$this->tableEmployee. '.name as drivername,
                    '.$this->tableCustomer. '.name as customername,
                    '.$this->tableConsignee. '.name as consigneename
				from
					'.$this->tableName.' 
                        left join  '.$this->tableSupplier. ' on '.$this->tableName. '.supplierkey = '.$this->tableSupplier. '.pkey 
                        left join  '.$this->tableTruckingServiceWorkOrder. ' on '.$this->tableName. '.pkey = '.$this->tableTruckingServiceWorkOrder. '.carkey and '.$this->tableTruckingServiceWorkOrder. '.statuskey in(1,2)
                        left join  '.$this->tableEmployee. ' on '.$this->tableTruckingServiceWorkOrder. '.driverkey =   '.$this->tableEmployee.'.pkey  
                        left join  '.$this->tableTruckingServiceJobOrder. ' on '.$this->tableTruckingServiceWorkOrder. '.refkey = '.$this->tableTruckingServiceJobOrder. '.pkey 
                        left join  '.$this->tableCustomer. ' on '.$this->tableTruckingServiceJobOrder. '.customerkey =   '.$this->tableCustomer.'.pkey
                        left join  '.$this->tableConsignee. ' on '.$this->tableTruckingServiceJobOrder. '.consigneekey =   '.$this->tableConsignee.'.pkey, 
                    '.$this->tableStatus.',
                    '.$this->tableWarehouse.',
                    '.$this->tableCategory.' 
                where
					'.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey and
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
					'.$this->tableName.'.categorykey = '.$this->tableCategory.'.pkey
 		';
        
        if (!empty($criteria))  
            $sql .=  ' ' .$criteria; 
        
        if (!empty($pkey))  
            $sql .=  '  and '.$this->tableName.'.pkey = ' .$this->oDbCon->paramString($pkey);
        
        if (!empty($order))  
            $sql .=  ' ' .$order;
         
        $rs =  $this->oDbCon->doQuery($sql);
         
        return $rs;
        
    }

    function getExpiryLicense($licenseType = array(), $warehousekey = ''){
        $rs = array();
        
        $arrSQL = array();
        
        $basesql = 'select code,policenumber, ';
        
		$arrType = array_column($licenseType,null,'dbfield');
		
		foreach($arrType as $row){ 
			$sql = $basesql . ' \''.$row['label'].'\' as typename, 
				  '.$row['dbfield'].' as expireddate
				  from ' . $this->tableName .' 
				  where
				  	' . $this->tableName .'.statuskey = 1 and
				  datediff('.$row['dbfield'].', now()) < ' . $this->oDbCon->paramString( $row['duedays'] ) ;
			
			if (!empty($warehousekey))
				$sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').' )';
			
            array_push($arrSQL, $sql);
		}
	   
        $sql = implode(' UNION ALL ', $arrSQL);
		 
        $sql = 'select *, datediff(expireddate,now()) as duedate from ('.$sql.' ) expired_license order by expireddate asc, policenumber asc';
     
        $rs =  $this->oDbCon->doQuery($sql);
         
        return $rs;
    }
    
    function normalizeParameter($arrParam, $trim=false){ 
         
        $warehouse = new Warehouse();
        
        $arrParam['selWarehouse'] = (isset($arrParam['selWarehouse'])) ? $arrParam['selWarehouse'] : $warehouse->getDefaultData() ; 
        $arrParam['policeNumber'] = $this->normalizePoliceNumber($arrParam['policeNumber']);
        
        $arrParam['cbm'] = $arrParam['length'] * $arrParam['width'] * $arrParam['height'] ;
        if(!$arrParam['chkIsVendor']){
            $arrParam['hidSupplierKey'] = '';
            $arrParam['selCarContract'] = VEHICLE_PARTNERSHIP_TYPE['oncall'];
            $arrParam['selCommissionType'] = 1; 
            $arrParam['commissionValue'] = 0;
        }
        
       
        if($this->activeModule['carservicemaintenance']){

                foreach ($arrParam['hidSparepartTypeKey'] as $i => $typeKey) {

                    $arrParam['hidSparepartTypeKey'][$i] = $typeKey;

                    // Assign mileage & month, ambil index 0 karena form input hanya 1 per type
                    $arrParam['mileage'][$i] = $arrParam['mileage_'.$typeKey][0] ?? null;
                    $arrParam['month'][$i]   = $arrParam['month_'.$typeKey][0] ?? null;

                    $arrParam['hidDetailSparePartIntervalKey'][$i] = 
                    $arrParam['hidDetailSparePartIntervalKey_'.$typeKey][0] ?? null;
                }
        }
        
        $arrParam = parent::normalizeParameter($arrParam,true); 
        
      /*
        $arrParam['bpkbName'] = (isset($arrParam['bpkbName'])) ? $arrParam['bpkbName'] : "" ; 
        $arrParam['bpkbNumber'] = (isset($arrParam['bpkbNumber'])) ? $arrParam['bpkbNumber'] : "" ; 
        $arrParam['licenseNumber'] = (isset($arrParam['licenseNumber'])) ? $arrParam['licenseNumber'] : "" ; 
        $arrParam['machineNumber'] = (isset($arrParam['machineNumber'])) ? $arrParam['machineNumber'] : "" ;
        $arrParam['chassisNumber'] = (isset($arrParam['chassisNumber'])) ? $arrParam['chassisNumber'] : "" ;
        $arrParam['kir'] = (isset($arrParam['kir'])) ? $arrParam['kir'] : "" ;
        $arrParam['kirExpiryDate'] = (isset($arrParam['kirExpiryDate'])) ? $arrParam['kirExpiryDate'] : '01 / 01 / 1970' ;
        $arrParam['tid'] = (isset($arrParam['tid'])) ? $arrParam['tid'] : "" ;
        $arrParam['tidExpiryDate'] = (isset($arrParam['tidExpiryDate'])) ? $arrParam['tidExpiryDate'] : '01 / 01 / 1970' ;
        $arrParam['hidCarSeriesKey'] = (isset($arrParam['hidCarSeriesKey'])) ? $arrParam['hidCarSeriesKey'] : 0 ; 
        $arrParam['capacity'] = (isset($arrParam['capacity'])) ? $arrParam['capacity'] : 0 ;
        $arrParam['fuelType'] = (isset($arrParam['fuelType'])) ? $arrParam['fuelType'] : 0 ;
        $arrParam['licenseExpiryDate'] = (isset($arrParam['licenseExpiryDate'])) ? $arrParam['licenseExpiryDate'] : '01 / 01 / 1970' ;
        $arrParam['licenseTaxExpiryDate'] = (isset($arrParam['licenseTaxExpiryDate'])) ? $arrParam['licenseTaxExpiryDate'] : '01 / 01 / 1970' ;
        $arrParam['isassets'] = (isset($arrParam['isassets']) && !empty($arrParam['isassets']) ) ? 1 : 0;
        $arrParam['hidCustomerKey'] = (isset($arrParam['hidCustomerKey']) && !empty($arrParam['hidCustomerKey'])) ? $arrParam['hidCustomerKey'] : 0 ; */
     
       
        
        
        return $arrParam; 
    }
    
    function getPartnershipType(){
        $sql = 'select * from '.$this->tablePartnershipType.' where statuskey = 1';
        return $this->oDbCon->doQuery($sql);
    }
	
	function getGPSInformation($param=array()){
		 
        $pkey = (isset($param['pkey'])) ? $param['pkey']:  array();
        $registrationNumber = (isset($param['registrationNumber'])) ? $param['registrationNumber']: array();
        $gpsProviderKey = (isset($param['gpsProviderKey'])) ? $param['gpsProviderKey']: array();
        $warehousekey = (isset($param['warehousekey'])) ? $param['warehousekey']: array();
        
        
        // normalize plat no
        foreach($registrationNumber as $key=>$row)
            $registrationNumber[$key] = $this->normalizePoliceNumber($row);
        
        
		if($this->isJson($pkey))
			$pkey = json_decode($pkey);
        
		if($this->isJson($registrationNumber))
			$registrationNumber = json_decode($registrationNumber);
		
		if($this->isJson($gpsProviderKey))
			$gpsProviderKey = json_decode($gpsProviderKey);

		if($this->isJson($warehousekey))
			$warehousekey = json_decode($warehousekey);
        
        
		// biar lebih sedikit querynya
		$sql = 'select 
					lower('.$this->tableGPS.'.code) as providercode,
					'.$this->tableGPS.'.name as providername,
					'.$this->tableName.'.pkey,
					'.$this->tableName.'.code,
					'.$this->tableName.'.policenumber,
					'.$this->tableName.'.gpstrackerid
				from '.$this->tableName.','.$this->tableGPS.'
				where 
					'.$this->tableGPS.'.statuskey = 1 and
					'.$this->tableName.'.gpskey = '.$this->tableGPS.'.pkey 
				';
        if (!empty($pkey))
           $sql .= ' and '.$this->tableName.'.pkey in ('. $this->oDbCon->paramString($pkey,',').')'; 
		
		if (!empty($registrationNumber))
			$sql .= ' and '.$this->tableName.'.policenumber in ('. $this->oDbCon->paramString($registrationNumber,',').')'; 
		 
        if (!empty($gpsProviderKey))
		  $sql .= ' and '.$this->tableName.'.gpskey in ('. $this->oDbCon->paramString($gpsProviderKey,',').')'; 

        if (!empty($warehousekey))
		  $sql .= ' and '.$this->tableName.'.warehousekey in ('. $this->oDbCon->paramString($warehousekey,',').')';
 
        $sql .=  $this->getWarehouseCriteria() ;
		
		return  $this->oDbCon->doQuery($sql);
	}
    
    function getMileage($trDate,$carkey){
        $sql = 'select * from  '.$this->tableMileage.' 
                where '.$this->tableMileage.'.trdate <= ' . $this->oDbCon->paramDate($trDate);
        
        // sementara gk bisa beberapa mobil sekaligus, karena limit 1 
        $sql .= ' and '.$this->tableMileage.'.refkey  = ' . $this->oDbCon->paramString($carkey);
        
        //if(!empty($registrationNumber))
        //    $sql .= ' and '.$this->tableMileage.'.refkey in ('.$this->oDbCon->paramString($registrationNumber,',').')';
        
        $sql .= ' order by  date('.$this->tableMileage.'.trdate) desc, mileage asc limit 1'; // kalo sehari kedepan bisa save beberapa ,ileage, ambil mileage terkecil di hari tersebut
        
        $rs = $this->oDbCon->doQuery($sql);
        return (!empty($rs)) ? $rs[0]['mileage'] : 0;
    }
    
    function searchMileage($starDate,$endDate, $registrationNumber=array()){
        // utk cari sudah ad bl mmileagenya ketika mau save log dari GPS
        if(!empty($registrationNumber) && !is_array($registrationNumber))
            $registrationNumber = array($registrationNumber);
        
        $sql = 'select * from '.$this->tableMileage.' where '.$this->tableMileage.'.trdate between ' . $this->oDbCon->paramDate($starDate). ' and ' .$this->oDbCon->paramDate($endDate);
        
        if(!empty($registrationNumber))
            $sql .= ' and '.$this->tableMileage.'.refkey in ('.$this->oDbCon->paramString($registrationNumber,',').')';
        
        return $this->oDbCon->doQuery($sql);
        
    }
    
    
    function getGPSMileage($arrParam){
    
        $GPSConnection = new GPSConnection();
        $rsMileage = $GPSConnection->getMileageData($arrParam); 
        
        return $rsMileage;
    }
    
    function updateMileage($arrParam){
         
        // tarik histori mileage, agar kalo sudah ad jgn diinsert lg
        $rsExistingMileage = $this->searchMileage($arrParam['startDate'], $arrParam['endDate']);
        $rsExistingMileageVehicleKey = array();
        foreach($rsExistingMileage as $row){
             array_push($rsExistingMileageVehicleKey, $row['refkey'].'-'.$row['trdate']);
        }
        
        // update semua mileage mobil
        $rsMileage = $this->getGPSMileage($arrParam);
        
        // get ulang list mobil
        $rsCar = $this->searchDataRow(array($this->tableName.'.pkey', $this->tableName.'.policenumber'), 
                                    ' and '.$this->tableName.'.statuskey in (1)' );
        
        foreach($rsCar as $key=>$row)
            $rsCar[$key]['policenumber'] = $this->standardizeRegistrationNumber( $rsCar[$key]['policenumber'] );
        
        $rsCar = array_column($rsCar,'pkey','policenumber');
        
        // loop dulu setiap mobil utk dapat milage diawal hari (kalo lebih dari 1 hari)
        $arrDay = array();
        foreach($rsMileage as $policenumber=>$row){ 
            if(!isset( $rsCar[$policenumber])) continue; // kalo tdk terdaftar di master mobil
            
            foreach($row as $detailRow){
                $formattedDate = date('Y-m-d', strtotime($detailRow['trdate']));
                $index = $policenumber.'-'.$formattedDate;
                // ambil yg pertama saja utk setiap hari
                
                if(!isset($arrDay[$index])) 
                    $arrDay[$index] = array('policenumber' => $policenumber,'carkey'=>$rsCar[$policenumber] , 'trdate' => $formattedDate, 'mileage' => $detailRow['mileage']); 
                
                // gk boleh break, karena mungkin ad tgl lain di index selanjutnya
                
            }
             
        }
        
        try{  

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]); 
 

            foreach($arrDay as $row){ 
                
                if(in_array($row['carkey'].'-'.$row['trdate'],$rsExistingMileageVehicleKey)) continue;
                
                $sql = 'insert into '.$this->tableMileage.' (refkey,trdate,mileage)
                        values('.$this->oDbCon->paramString($row['carkey']).','.$this->oDbCon->paramString($row['trdate']).' , '.$this->oDbCon->paramString($row['mileage']).' )';
                
                $this->oDbCon->execute($sql);

            }

            $this->oDbCon->endTrans();   

        } catch(Exception $e){
            $this->oDbCon->rollback();  
        }	 
          
    }
	

    function getCarSparePartIntervalDetail($car, $spareparttypekey = '')
    {
            $sql = 'select 
                        '. $this->tableSparePartIntervalDetail. '.*,
                        '. $this->tableSparePartType.'.name as spareparttypename
                  from 
                        ' . $this->tableSparePartIntervalDetail. ',
                        ' . $this->tableSparePartType. '
                  where 
                        ' . $this->tableSparePartIntervalDetail .'.spareparttypekey = ' . $this->tableSparePartType .'.pkey  and
                        '. $this->tableSparePartIntervalDetail.'.refkey in (' . $this->oDbCon->paramString($car,',').')
            ';

            if(!empty($spareparttypekey)){
                  $sql .= ' and ' . $this->tableSparePartIntervalDetail .'.spareparttypekey in (' . $this->oDbCon->paramString($spareparttypekey,',').') ';
            }
            
            $rs = $this->oDbCon->doQuery($sql); 
      
            return $rs;
    }
 

    function getCarInterval($carkey, $spareparttypekey = '')
    {
        $carCategory = new CarCategory();

        $rs = $this->getDataRowById($carkey);

        if(empty($rs)) return;

        $sql = '
            select
                '.$this->tableSparePartIntervalDetail.'.refkey as carkey,
                '.$this->tableSparePartIntervalDetail.'.spareparttypekey,
                '.$this->tableSparePartIntervalDetail.'.mileage,
                '.$this->tableSparePartIntervalDetail.'.month
            from
                '.$this->tableSparePartIntervalDetail.'
            where
                '.$this->tableSparePartIntervalDetail.'.refkey = '.$this->oDbCon->paramString($rs[0]['pkey']).'
        ';

        if(!empty($categorykey)) {
            $sql .= ' and ' .$this->tableSparePartIntervalDetail.'.spareparttypekey in ('.$this->oDbCon->paramString($spareparttypekey,',').') ';
        }

        $rsInterval = $this->oDbCon->doQuery($sql);


        //dari category mobil
        $rsIntervalCategory = $carCategory->getSparePartIntervalDetail($rs[0]['categorykey'], $spareparttypekey);

        $rsData = [];

        if(empty($rsInterval)) {
            foreach ($rsIntervalCategory as $catRow) {
                $rsData[] = [
                    'carkey' => $rs[0]['pkey'],
                    'spareparttypekey' => $catRow['spareparttypekey'],
                    'mileage' => $catRow['mileage'],
                    'month' => $catRow['month'],
                ];
            }
        } else {

            foreach ($rsIntervalCategory as $catRow) {

                $carRow = null;
                foreach ($rsInterval as $r) {
                    if ($r['spareparttypekey'] == $catRow['spareparttypekey']) {
                        $carRow = $r;
                        break;
                    }
                }

                if (!$carRow) {
                    $rsData[] = [
                        'carkey' => $rs[0]['pkey'], 
                        'spareparttypekey' => $catRow['spareparttypekey'],
                        'mileage' => $catRow['mileage'],
                        'month' => $catRow['month'],
                    ];
                    continue;
                }

                $rsData[] = [
                    'carkey' => $carRow['carkey'],
                    'spareparttypekey' => $catRow['spareparttypekey'],
                    'mileage' => $carRow['mileage'] ?: $catRow['mileage'],
                    'month' => $carRow['month'] ?: $catRow['month'],
                ];
            }

        }

        return $rsData;
    }

    function getCarItemDetail($pkey, $criteria = '') 
    {
        $sql = '
            select
                '.$this->tableCarItemDetail.'.*,
                '.$this->tableName.'.policenumber,
                '.$this->tableItem.'.name as itemname,
                '.$this->tableSparePartType.'.name as spareparttype
            from
                '.$this->tableCarItemDetail.'
                    left join '.$this->tableSparePartType.' on '.$this->tableCarItemDetail.'.spareparttypekey = '.$this->tableSparePartType.'.pkey,
                '.$this->tableName.',
                '.$this->tableItem.'
            where
                '.$this->tableCarItemDetail.'.refkey = '.$this->tableName.'.pkey and
                '.$this->tableCarItemDetail.'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableCarItemDetail.'.refkey in ('.$this->oDbCon->paramString($pkey,',').')
        ';

        if(!empty($criteria)) {
            $sql .= ' ' . $criteria;
        }

        $rs = $this->oDbCon->doQuery($sql); 

        return $rs;
    }

    function updateCarItemDetail($refkey)
    {
        // nanti dicek lg
        return ;
        
        $carServiceMaintenance = new CarServiceMaintenance();
        
        $rsCar = $this->getDataRowById($refkey);
        if(empty($rsCar)) {
            throw new Exception('<strong>'.$this->lang['car']. '</strong>. ' .$this->errorMsg[213]);
        }
        
        $rsCarItem = $this->getCarItemDetail($refkey);
        if(!empty($rsCarItem)) {
            $this->deleteCarItemDetail($refkey);
        }

        //cari maintenance by car
        $rsMaintenance = $carServiceMaintenance->searchDataRow(array(
            $carServiceMaintenance->tableName.'.pkey',
            $carServiceMaintenance->tableName.'.code',
            $carServiceMaintenance->tableName.'.trdate',
            $carServiceMaintenance->tableName.'.carkey',
            $carServiceMaintenance->tableName.'.statuskey'
        ), ' and ' . $carServiceMaintenance->tableName.'.carkey = '.$this->oDbCon->paramString($refkey).' and '.$carServiceMaintenance->tableName.'.statuskey in (2,3)');

        if(empty($rsMaintenance)) {
            throw new Exception('<strong>'.$this->lang['carServiceMaintenance']. '</strong>. ' .$this->errorMsg[213]);
        } else {

            $arrMaintenanceKeys = array_column($rsMaintenance,'pkey');
            $rsMaintenanceCols = $this->reindexDetailCollections($rsMaintenance,'pkey');

            $rsMaintenanceDetail = $carServiceMaintenance->getDetailWithRelatedInformation($arrMaintenanceKeys);

            for($i=0;$i<count($rsMaintenanceDetail); $i++) {

                if(!isset($rsMaintenanceCols[$rsMaintenanceDetail[$i]['refkey']])) continue;
                $rsMaintenanceCol = $rsMaintenanceCols[$rsMaintenanceDetail[$i]['refkey']];

                $date = $rsMaintenanceCol[0]['trdate'];
                $maintenancekey = $rsMaintenanceDetail[$i]['refkey'];
                $itemkey = $rsMaintenanceDetail[$i]['itemkey'];
                $spareparttypekey = $rsMaintenanceDetail[$i]['spareparttypekey'];
                $positionkey  = $rsMaintenanceDetail[$i]['itemposition'];
                $serialnumber = $rsMaintenanceDetail[$i]['newsn'];

                $this->addCarItemDetail($refkey, $date, $maintenancekey, $itemkey, $spareparttypekey, $positionkey, $serialnumber);

            }

        }


    }

    function addCarItemDetail($refkey, $date, $maintenancekey, $itemkey, $spareparttypekey, $positionkey, $serialnumber)
    {
        $sql = '
            insert into '.$this->tableCarItemDetail.'
                (refkey,trdate,maintenancekey,itemkey,spareparttypekey,positionkey,serialnumber)
            values (
                '.$this->oDbCon->paramString($refkey).',
                '.$this->oDbCon->paramString($date).',
                '.$this->oDbCon->paramString($maintenancekey).',
                '.$this->oDbCon->paramString($itemkey).',
                '.$this->oDbCon->paramString($spareparttypekey).',
                '.$this->oDbCon->paramString($positionkey).',
                '.$this->oDbCon->paramString($serialnumber).'
            )';

        $this->oDbCon->execute($sql);
    }

    function deleteCarItemDetail($refkey)
    {
        $sql = '
            delete from '.$this->tableCarItemDetail.' where '.$this->tableCarItemDetail.'.refkey = '.$this->oDbCon->paramString($refkey).' 
        ';

        $this->oDbCon->execute($sql);
    }
    
    function getCarItemLastSN($carkey, $itemkey, $positionkey)
    {

        $item = new Item();

        $rsItemSparepartType =  $item->getItemSparepartTypeKey($itemkey);
        // $item->searchData('','',true,' and ' . $item->tableName . '.pkey = ' . $item->oDbCon->paramString($itemkey).' ');
        
        
        $spareparttypekey = (!empty($rsItemSparepartType)) ? $rsItemSparepartType[0]['spareparttypekey'] : 0;

        if(empty($spareparttypekey)) return;

        $sql = '
            select
                '.$this->tableCarItemDetail.'.*,
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.name as itemname
            from
                '.$this->tableCarItemDetail.',
                '.$this->tableItem.'
            where
                '.$this->tableCarItemDetail.'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableCarItemDetail.'.refkey = '.$this->oDbCon->paramString($carkey).' and
                '.$this->tableCarItemDetail.'.itemkey = '.$this->oDbCon->paramString($itemkey).' and
                '.$this->tableCarItemDetail.'.positionkey = '.$this->oDbCon->paramString($positionkey).' and
                '.$this->tableCarItemDetail.'.spareparttypekey = '.$this->oDbCon->paramString($spareparttypekey).'
                order by trdate desc, pkey desc limit 1
        ';
    
        $rs = $this->oDbCon->doQuery($sql); 

        return $rs;

    }

    function getCarItemDetailForPrint($pkey)
    {
        $sql = '
            select
                '.$this->tableCarItemDetail.'.*,
                '.$this->tableName.'.policenumber,
                '.$this->tableItem.'.code as itemcode,
                '.$this->tableItem.'.name as itemname,
                '.$this->tableSparePartType.'.name as spareparttype,
                '.$this->tableItemPosition.'.name as positionname,
                CONCAT('.$this->tableCarItemDetail.'.refkey,\'_\','.$this->tableCarItemDetail.'.itemkey,\'_\','.$this->tableCarItemDetail.'.positionkey) as indexkey
            from
                '.$this->tableCarItemDetail.'
                    left join '.$this->tableSparePartType.' on '.$this->tableCarItemDetail.'.spareparttypekey = '.$this->tableSparePartType.'.pkey
                    left join '.$this->tableItemPosition.' on '.$this->tableCarItemDetail.'.positionkey = '.$this->tableItemPosition.'.pkey,
                '.$this->tableName.',
                '.$this->tableItem.'
            where
                '.$this->tableCarItemDetail.'.refkey = '.$this->tableName.'.pkey and
                '.$this->tableCarItemDetail.'.itemkey = '.$this->tableItem.'.pkey and
                '.$this->tableCarItemDetail.'.refkey = '.$this->oDbCon->paramString($pkey).'
                order by trdate desc, pkey desc
        ';

        $rs = $this->oDbCon->doQuery($sql); 

        if(empty($rs)) return;

        // $latest = [];
        // foreach ($rs as $row) {
        //     $indexkey = $row['indexkey'];
        //     if (!isset($latest[$indexkey])) {
        //         $latest[$indexkey] = $row;
        //         continue;
        //     }
        //     $current = $latest[$indexkey];
        //     // Bandingkan trdate
        //     if ($row['trdate'] > $current['trdate']) {
        //         $latest[$indexkey] = $row;
        //         continue;
        //     }
        //     // Jika trdate sama, bandingkan pkey
        //     if ($row['trdate'] == $current['trdate'] && $row['pkey'] > $current['pkey']) {
        //         $latest[$indexkey] = $row;
        //         continue;
        //     }
        // }
        // $result = array_values($latest);

        $result = [];
        foreach ($rs as $row) {
            $key = $row['indexkey'];
            if (!isset($result[$key])) {
                $result[$key] = $row;
            }
        }

        return array_values($result);
    }
}
?>
