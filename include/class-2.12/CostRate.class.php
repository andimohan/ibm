<?php
  
class CostRate extends BaseClass{ 
  
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'cost_rate_header';
		$this->tableNameDetail = 'cost_rate_detail'; 
        $this->tableJobPriceDetail = 'cost_rate_detail_job_price';
		$this->tableCostDetail = 'cost_rate_detail_cost';
		$this->tableTruckingJob = 'trucking_job';
        $this->tableCargoType = 'cargo_type';
		$this->tableLocation = 'location'; 
		$this->tableWarehouse = 'warehouse'; 
        $this->tableConsignee = 'consignee';
		$this->tableStatus = 'master_status'; 
        $this->tableItem = 'item';
        $this->tableCarCategory = 'car_category';
       
		$this->tableNeedToBeCopyOnCancel = array($this->tableNameDetail);
       
		$this->securityObject = 'CostRate';   
       
        $this->costRateUseCarCategory = $this->loadSetting('costRateUseCarCategory');
        $this->costByIndexField = ($this->costRateUseCarCategory == 1) ? 'carcategorykey'  : 'itemkey';

         // untuk komisi sopir
        $this->rsDriverCommission = array();
        array_push($this->rsDriverCommission, array('pkey' => -1 , 'name' => $this->lang['driverCommission'], 'fixedcost' => 1));
        array_push($this->rsDriverCommission, array('pkey' => -2, 'name' => $this->lang['codriverCommission'], 'fixedcost' => 1));
       
       
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableNameDetail,'field' => 'refkey'));
        array_push($this->arrDeleteTable, array('table'=>$this->tableJobPriceDetail,'field' => 'refheaderkey'));  
        array_push($this->arrDeleteTable, array('table'=>$this->tableCostDetail,'field' => 'refheaderkey'));   
        array_push($this->filterCriteria, array('title' => $this->lang['warehouse'], 'field' => 'warehousekey'));
       
                
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'warehouse','title' => 'warehouse','dbfield' => 'warehousename','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'consignee','title' => 'consignee','dbfield' => 'consigneename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'cargoType','title' => 'cargoType','dbfield' => 'cargotypename','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'location','title' => 'location','dbfield' => 'location','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc', 'width' => 200));
     
        $this->includeClassDependencies(array( 
            'Service.class.php',
            'Warehouse.class.php',
            'City.class.php',
            'Consignee.class.php',
            'Location.class.php',
            'TruckingJob.class.php',
            'CarCategory.class.php'
        ));  
       
        $this->overwriteConfig();
       
   }
   
   function getQuery(){
	   
	   $sql = '
			SELECT 
               '.$this->tableName.'.* ,   
               '.$this->tableLocation.'.name as location,
               '.$this->tableCargoType.'.name as cargotypename  ,
               '.$this->tableConsignee.'.name as consigneename  ,
			   '.$this->tableStatus.'.status as statusname ,
			   '.$this->tableWarehouse.'.name as warehousename 
			FROM 
                '.$this->tableName . '
                    left join '.$this->tableWarehouse.' on '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey 
                    left join '.$this->tableConsignee.' on '.$this->tableName.'.consigneekey = '.$this->tableConsignee.'.pkey ,
                '.$this->tableLocation.',
                '.$this->tableCargoType.',
                '.$this->tableStatus.' 
			WHERE  
                '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey and   
                '.$this->tableName . '.cargotypekey = '.$this->tableCargoType.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey 
 		' .$this->criteria ; 
		 
       $sql .=  $this->getWarehouseCriteria();
       return $sql;
       
    }  
	
   function addData($arrParam){
	   
		$arrayToJs = array(); 
		try{						

                if(!$this->oDbCon->startTrans())
                    throw new Exception($this->errorMsg[100]);
 
                $code = $this->getNewCustomCode($arrParam);	 
                $arrParam['code'] = (is_array($code)) ? $code[0] : $code;

                $arrayToJs = $this->validateForm($arrParam);
                if (!empty($arrayToJs)) 
                        return $arrayToJs;
 
				$pkey = $this->getNextKey($this->tableName); 
					
                $sql = '
						INSERT INTO		
						 '.$this->tableName .' (
                            pkey, 
							code, 
                            name,
                            cargotypekey,
                            locationkey, 
                            warehousekey, 
                            consigneekey, 
							statuskey,
                            trdesc,
							createdby,
							createdon,
                            modifiedon
						)
						VALUES	( 
							'.$pkey.', 
							'.$this->oDbCon->paramString($arrParam['code']).', 
							'.$this->oDbCon->paramString($arrParam['name']).', 
							'.$this->oDbCon->paramString($arrParam['hidCargoTypeKey']).',    
							'.$this->oDbCon->paramString($arrParam['hidLocationKey']).',    
							'.$this->oDbCon->paramString($arrParam['selWarehouseKey']).',    
							'.$this->oDbCon->paramString($arrParam['hidConsigneeKey']).',    
							1,
							'.$this->oDbCon->paramString($arrParam['trDesc']).',   
							'.$this->oDbCon->paramString($arrParam['createdBy']).', 
							now(),
                            now()
						)
				';
			 
				$this->oDbCon->execute($sql);        
				$this->updateDetail($pkey, $arrParam);	 
                $this->setTransactionLog(INSERT_DATA,$pkey);
              
				$this->oDbCon->endTrans(); 
						
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
		}		
		
		return $arrayToJs; 
			
	}
    
	
        
    function editData($arrParam){
	 
		$arrayToJs = array(); 
		
		try{  
			
				if(!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
				
                
                $code = $this->getNewCustomCode($arrParam);	 
                $arrParam['code'] = (is_array($code)) ? $code[0] : $code;

				$arrayToJs = $this->validateForm($arrParam,$arrParam['hidId']);
				if (!empty($arrayToJs)) 
						return $arrayToJs; 
            
				$sql = '
						UPDATE	
						 '.$this->tableName .'
						SET	  
							code = '.$this->oDbCon->paramString($arrParam['code']).',  
							name = '.$this->oDbCon->paramString($arrParam['name']).',  
							cargotypekey = '.$this->oDbCon->paramString($arrParam['hidCargoTypeKey']).',    
                            locationkey = '.$this->oDbCon->paramString($arrParam['hidLocationKey']).',   
                            warehousekey = '.$this->oDbCon->paramString($arrParam['selWarehouseKey']).',   
                            consigneekey = '.$this->oDbCon->paramString($arrParam['hidConsigneeKey']).',   
                            trdesc = '.$this->oDbCon->paramString($arrParam['trDesc']).',  
							modifiedby = '.$this->oDbCon->paramString($arrParam['modifiedBy']).', 
							modifiedon = now()  
						WHERE	
						 pkey = '.$this->oDbCon->paramString($arrParam['hidId']).'
				';
						 										   
				$this->oDbCon->execute($sql);
				$this->updateDetail($arrParam['hidId'], $arrParam);    
					
                $this->setTransactionLog(UPDATE_DATA,$arrParam['hidId']);
            
				$this->oDbCon->endTrans();
					 		
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);   

		}catch(Exception $e){
			$this->oDbCon->rollback();
			$this->addErrorList($arrayToJs,false,$e->getMessage());    
		}		
		
		return $arrayToJs; 
		 

	}
      
	
    function updateDetail($pkey,$arrParam){ 
        $cost = new Service(TRUCKING_SERVICE,1);
        $truckingJob = new TruckingJob();
        //$this->updateJob($pkey, $arrParam); 
        
	 	$sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql); 
         
        $sql = 'delete from '.$this->tableJobPriceDetail.' where refheaderkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
	 	
        $sql = 'delete from '.$this->tableCostDetail.' where refheaderkey = '. $this->oDbCon->paramString($pkey);
		$this->oDbCon->execute($sql);
        
		 
		$arrJobTypeKey = $arrParam['hidJobTypeKey'];
        $arrItemKey = $arrParam['hidItemKey'];
        
        // untuk komisi sopir  
        $rsCost = $cost->searchData($cost->tableName.'.statuskey',1, true, ' and showincostrate = 1 and chargetype = 2','order by fixedcost desc, name asc');  
        $rsCost = array_merge($this->rsDriverCommission, $rsCost);
         
     	for ($i=0;$i<count($arrJobTypeKey);$i++){
			 
            $detailkey = $this->getNextKey($this->tableNameDetail); 
            if (empty($arrJobTypeKey[$i]))
                continue;
			 
			$sql = 'insert into '.$this->tableNameDetail.' (
                        pkey,
						refkey,
                        jobtypekey 
					 ) values (
						'.$this->oDbCon->paramString($detailkey).',
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrJobTypeKey[$i]).' 
					)';	 
            
			$this->oDbCon->execute($sql); 
 
            // insert untuk komisi dulu
            
              for ($j=0;$j<count($arrItemKey);$j++){ 
                 
                for ($k=0;$k<count($rsCost);$k++){
                        $arrCostPrice = $arrParam['cost_'.$rsCost[$k]['pkey'].'_'.$arrItemKey[$j]];  
                        $price = $this->unFormatNumber($arrCostPrice[$i]);
                        
                        if (empty($price))
                            continue;
                        
                        $sql = 'insert into '.$this->tableCostDetail.' ( 
                                    refkey,
                                    refheaderkey, 
                                    costkey,
                                    '.$this->costByIndexField.',
                                    price
                                 ) values (  
                                    '.$this->oDbCon->paramString($detailkey).',
                                    '.$this->oDbCon->paramString($pkey).',
                                    '.$this->oDbCon->paramString($rsCost[$k]['pkey']).', 
                                    '.$this->oDbCon->paramString($arrItemKey[$j]).', 
                                    '.$this->oDbCon->paramString($price).'
                                )';	 
                         
                       $this->oDbCon->execute($sql);
                     
                    } 
            }
  
		} 
					
	}
    
    
     function validateForm($arr,$pkey = ''){ 
	    $truckingJob = new TruckingJob();
         
		$arrayToJs = parent::validateForm($arr,$pkey); 
          
		$name = $arr['name'];  
		$locationkey = $arr['hidLocationKey'];  
		$cargokey = $arr['hidCargoTypeKey'];  
		$arrJobTypeKey = $arr['hidJobTypeKey'];   
           
         
         if(empty($name)){  
				$this->addErrorList($arrayToJs,false,$this->errorMsg['name'][1]);
         }
         
         if(empty($locationkey)){  
				$this->addErrorList($arrayToJs,false,$this->errorMsg['location'][1]);
         }
         
         if(empty($cargokey)){  
				$this->addErrorList($arrayToJs,false,$this->errorMsg['cargoType'][1]);
         }
         
       
        $arrDetailKeys = array(); 
		for($i=0;$i<count($arrJobTypeKey);$i++) { 
            
            $rsJob = $truckingJob->getDataRowById($arrJobTypeKey[$i]);  
            
			if (empty($arrJobTypeKey[$i]) ){ 
				$this->addErrorList($arrayToJs,false, $this->errorMsg['jobType'][1]); 	
			} 
			  
            // cek ada detail double gk  
            if (in_array($arrJobTypeKey[$i],$arrDetailKeys)){   
                $this->addErrorList($arrayToJs,false, $rsJob[0]['name'].'. '.$this->errorMsg[215]); 	 
            }else{  
                array_push($arrDetailKeys, $arrJobTypeKey[$i]);
            } 
           
		}
       
		return $arrayToJs;
	 }
	   
   function getDetailWithRelatedInformation($pkey,$criteria=''){
   
       
         $sql = 'SELECT
                '.$this->tableName.'.pkey,
                '.$this->tableTruckingJob.'.name as jobtypename,   
                '.$this->tableNameDetail.'.jobtypekey 
                FROM 
                '.$this->tableName.',
                '.$this->tableNameDetail.',
                '.$this->tableTruckingJob.'
                WHERE 
                '.$this->tableName.'.pkey = '.$this->oDbCon->paramString($pkey).' and
                '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                '.$this->tableNameDetail.'.jobtypekey = '.$this->tableTruckingJob.'.pkey';
        
          
        $sql .= $criteria;
         
		return $this->oDbCon->doQuery($sql);
	
   }
    
   /*
    function getJobPrice($pkey,$jobtypekey='',$itemkey=''){
        $sql = 'select 
                    '.$this->tableJobPriceDetail.'.itemkey,
                    '.$this->tableJobPriceDetail.'.price
                from
                    '.$this->tableNameDetail.',
                    '.$this->tableJobPriceDetail.'
                where
                    '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey).' and  
                    '.$this->tableNameDetail.'.pkey = '.$this->tableJobPriceDetail.'.refkey ';
        
        if (!empty($jobtypekey))
            $sql .= ' and '.$this->tableNameDetail.'.jobtypekey = '. $this->oDbCon->paramString($jobtypekey);
         
        
        return $this->oDbCon->doQuery($sql);
    }*/
    
     function getJobCost($pkey,$jobtypekey='',$costkey ='', $itemkey=''){
        $sql = 'select 
                    '.$this->tableCostDetail.'.'.$this->costByIndexField.',
                    '.$this->tableCostDetail.'.price
                from
                    '.$this->tableName.', 
                    '.$this->tableNameDetail.', 
                    '.$this->tableCostDetail.'
                where
                    '.$this->tableName.'.pkey = '.$this->tableNameDetail.'.refkey and
                    '.$this->tableNameDetail.'.pkey = '.$this->tableCostDetail.'.refkey and
                    '.$this->tableNameDetail.'.refkey = '.$this->oDbCon->paramString($pkey);
        
        if (!empty($jobtypekey))
            $sql .= ' and '.$this->tableNameDetail.'.jobtypekey = '. $this->oDbCon->paramString($jobtypekey);
        
        if (!empty($costkey))
            $sql .= ' and '.$this->tableCostDetail.'.costkey = '. $this->oDbCon->paramString($costkey);
        
         
         $sql .=  $this->getWarehouseCriteria();
          
         return $this->oDbCon->doQuery($sql);
    }
    
    function getDriverCommissionRate($warehousekey, $locationKey, $cargoType, $jobTypeKey,$itemKey, $consigneekey='' ){
        // algo ssama dengan get cost, jd kalo criteria diubah, get cost jg harus berubah 
        
         $rsHeader = array();

        $sqlBasic = 'select 
                    '.$this->tableName.'.pkey
                from 
                    '.$this->tableName.'
                where
                    '.$this->tableName.'.statuskey = 1 and
                    '.$this->tableName.'.warehousekey = '.$this->oDbCon->paramString($warehousekey).' and
                    '.$this->tableName.'.locationkey = '.$this->oDbCon->paramString($locationKey).' and
                    '.$this->tableName.'.cargotypekey = '.$this->oDbCon->paramString($cargoType).' 

                    ';

        
        if($consigneekey){
            $sql = $sqlBasic . ' and  '.$this->tableName.'.consigneekey = '.$this->oDbCon->paramString($consigneekey);
            $sql .=  $this->getWarehouseCriteria(); 
            $sql .= ' order by createdon desc';
 
            $rsHeader = $this->oDbCon->doQuery($sql); 
        }
          
        if(empty($rsHeader)){
                
                $sql = $sqlBasic ;     
                $sql .=  $this->getWarehouseCriteria();  
                $sql .= 'order by consigneekey asc, createdon desc';
 
                $rsHeader = $this->oDbCon->doQuery($sql); 
        }        
        
        if(empty($rsHeader))    return array();
        $sql = 'select 
                    '. $this->tableName . '.locationkey,
                    '.$this->tableNameDetail.'.jobtypekey,
                    '.$this->tableCostDetail.'.'.$this->costByIndexField.',
                    '.$this->tableCostDetail.'.costkey,
                    '.$this->tableCostDetail.'.price 
                from
                    '.$this->tableNameDetail.', 
                    '.$this->tableCostDetail.'
                    left join ' . $this->tableName . ' on ' . $this->tableCostDetail . '.refheaderkey = ' . $this->tableName . '.pkey
                where
                    '.$this->tableNameDetail.'.jobtypekey = '.$this->oDbCon->paramString($jobTypeKey).' and   
                    '.$this->tableNameDetail.'.refkey =  '. $this->oDbCon->paramString($rsHeader[0]['pkey']).' and    
                    '.$this->tableCostDetail.'.'.$this->costByIndexField.' = '.$this->oDbCon->paramString($itemKey).' and   
                    '.$this->tableCostDetail.'.refkey = '. $this->tableNameDetail.'.pkey';

        $sql .= ' and '.$this->tableCostDetail.'.costkey in (-1,-2)' ; 
        $rsDetail = $this->oDbCon->doQuery($sql); 

        return $rsDetail; 
    }
    
    
    function getCostDetail($warehousekey, $locationKey, $cargoType, $jobTypeKey, $itemKey, $costKey = '', $consigneekey='' ){
     // algo ssama dengan get driver commission, jd kalo criteria diubah, get cost jg harus berubah 
        
        
        $rsHeader = array();

        $sqlBasic = 'select 
                    '.$this->tableName.'.pkey
                from 
                    '.$this->tableName.'
                where
                    '.$this->tableName.'.statuskey = 1 and
                    '.$this->tableName.'.warehousekey = '.$this->oDbCon->paramString($warehousekey).' and
                    '.$this->tableName.'.locationkey in ('.$this->oDbCon->paramString($locationKey,',').') and
                    '.$this->tableName.'.cargotypekey = '.$this->oDbCon->paramString($cargoType).' 

                    ';

        if($consigneekey){
            $sql = $sqlBasic . ' and  '.$this->tableName.'.consigneekey = '.$this->oDbCon->paramString($consigneekey);
            $sql .=  $this->getWarehouseCriteria(); 
            $sql .= ' order by createdon desc';
 
            $rsHeader = $this->oDbCon->doQuery($sql); 
        }
          
        //$this->setLog($sqlBasic,true);
        
        if(empty($rsHeader)){
                
      		    $arrCriteria = array();
                array_push($arrCriteria, $this->tableName.'.consigneekey = 0 ');
                $criteria = implode(' and ',$arrCriteria);   

                $sql = $sqlBasic ;     
                if (!empty($criteria)) {
                    $sql .= ' and '.$criteria;  
                }
                $sql .=  $this->getWarehouseCriteria();  
                $sql .= 'order by pkey, consigneekey asc, createdon desc '; // gk boleh limit 1, karena kepake di Praja
            
                // di ETI gk tau masalah tdk kalo gk limit 1, jd sementara kalo lokasinya cuma 1, pake limit 1
                if(!is_array($locationKey)){
                    $sql .= ' limit 1';
                }
  
                $rsHeader = $this->oDbCon->doQuery($sql); 
        }        
        
        if(empty($rsHeader))    return array();

        if($this->costRateUseCarCategory == 1) {
            $tableName = $this->tableCarCategory; 
            $aliasName = 'categoryname';
        } else {
            $tableName = $this->tableItem;
            $aliasName = 'servicename';
        }

        
 $sql = 'select 
                    '.$this->tableName.'.locationkey,
                    '.$this->tableNameDetail.'.jobtypekey,
                    '.$this->tableCostDetail.'.'.$this->costByIndexField.',
                    '.$this->tableCostDetail.'.costkey,
                    '.$this->tableCostDetail.'.price,
                    '.$tableName.'.name as '.$aliasName.'   
                from
                    '.$this->tableNameDetail.', 
                    '.$this->tableCostDetail.' 
                    left join '.$this->tableName.' on '.$this->tableCostDetail.'.refheaderkey = '.$this->tableName.'.pkey, 
                    '.$tableName.'
                where
                    '.$this->tableNameDetail.'.jobtypekey = '.$this->oDbCon->paramString($jobTypeKey).' and  
                    '.$tableName.'.pkey =  '.$this->oDbCon->paramString($itemKey).' and
                    '.$this->tableCostDetail.'.'.$this->costByIndexField.' =  '.$tableName.'.pkey and
                    '.$this->tableNameDetail.'.refkey in  ('. $this->oDbCon->paramString(array_column($rsHeader,'pkey'),',').') and     
                    '.$this->tableCostDetail.'.refkey = '. $this->tableNameDetail.'.pkey';


        if (!empty($costKey))
                 $sql .= ' and '.$this->tableCostDetail.'.costkey in ('. $this->oDbCon->paramString($costKey,',').')';
        
        $rsDetail = $this->oDbCon->doQuery($sql); 

        return $rsDetail; 
    }
    
      
    function delete($id,$forceDelete = false,$reason = ''){
		 
		$arrayToJs =  array(); 
		
		try{ 
		
	 		$arrayToJs = $this->validateDelete($id);
			if (!empty($arrayToJs)) 
				return $arrayToJs;
					 
			 if(!$this->oDbCon->startTrans())
				throw new Exception($this->errorMsg[100]);
				 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);
			 
                $this->deleteReference($id);
            
                $this->setTransactionLog(DELETE_DATA,$id);
            
				$this->oDbCon->endTrans(); 

				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']); 
				 
		} catch(Exception $e){
			$this->oDbCon->rollback(); 
			$this->addErrorList($arrayToJs,false, $e->getMessage()); 
			
		}		 
			 	
 		return $arrayToJs; 
	}
    
  function generateReportCostRate($searchCriteria='',$orderCriteria='', $group = ''){
        
        $sql = 'select 
                    '.$this->tableCostDetail.'.*,
                    '.$this->tableName.'.code,
                    '.$this->tableName.'.name,
                    '.$this->tableName.'.cargotypekey,
                    '.$this->tableName.'.locationkey,
                    '.$this->tableNameDetail.'.jobtypekey,
                    '.$this->tableLocation.'.name as location,
                    '.$this->tableCargoType.'.name as cargotypename,
                    '.$this->tableTruckingJob.'.name as jobtypename,
                    '.$this->tableWarehouse.'.name as warehousename,
                    '.$this->tableItem.'.name as costname,
                    '.$this->tableConsignee.'.name as consigneename                    
                from
                    '.$this->tableName.'
                        left join '.$this->tableConsignee.' on '.$this->tableName.'.consigneekey = '.$this->tableConsignee.'.pkey ,
                    '.$this->tableNameDetail.',
                    '.$this->tableCostDetail.' 
                        left join '.$this->tableItem.' on '.$this->tableCostDetail.'.costkey = '.$this->tableItem.'.pkey,
                    '.$this->tableTruckingJob.',
                    '.$this->tableCargoType.',
                    '.$this->tableWarehouse.',
                    '.$this->tableLocation.'
                where
                    
                    '.$this->tableNameDetail.'.refkey =  '. $this->tableName.'.pkey and 
                    '.$this->tableCostDetail.'.refkey = '. $this->tableNameDetail.'.pkey and 
                    '.$this->tableName.'.warehousekey = '.$this->tableWarehouse.'.pkey and
                    '.$this->tableName.'.locationkey = '.$this->tableLocation.'.pkey and
                    '.$this->tableName.'.cargotypekey = '.$this->tableCargoType.'.pkey and
                    '.$this->tableNameDetail.'.jobtypekey = '.$this->tableTruckingJob.'.pkey
                    ';
        
           if($searchCriteria <> '') $sql .= ' ' .$searchCriteria; 
           if($group <> '') $sql .= ' ' .$group;  
           if($orderCriteria <> '')  $sql .= ' ' .$orderCriteria;
           
//        $this->setLog($sql,true);
          return $this->oDbCon->doQuery($sql);

    }  
    
}
?>
