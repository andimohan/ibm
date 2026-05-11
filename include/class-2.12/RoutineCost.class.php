<?php 
class RoutineCost extends BaseClass{ 
 
    function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'routine_cost_header';
		$this->tableNameDetail = 'routine_cost_detail';
        $this->tableStatus = 'master_status';
        $this->tableSupplier = 'supplier';
        $this->tableChargeType = 'routine_cost_charge_type';
        $this->tableRepeatPeriod = 'repeat_periode';
        $this->securityObject = 'RoutineCost';
        
        $this->arrDataDetail = array();  
        $this->arrDataDetail['pkey'] = array('hidDetailKey');
        $this->arrDataDetail['refkey'] = array('pkey','ref');
        $this->arrDataDetail['description'] = array('description'); 
        $this->arrDataDetail['chargetype'] = array('selChargeType'); 
        $this->arrDataDetail['aptype'] = array('selTransactionType'); 
        $this->arrDataDetail['amount'] = array('amount', array('datatype'=>'number','mandatory'=>true)); 
        
        $arrDetails = array();
        array_push($arrDetails, array('dataset' => $this->arrDataDetail));
        
        $this->arrData = array();
        $this->arrData['pkey'] = array('pkey', array('dataDetail' => $arrDetails));
        //$this->arrData['trdate'] = array('trDate','date');
        $this->arrData['code'] = array('code');
        $this->arrData['supplierkey'] = array('hidSupplierKey'); 
        $this->arrData['trdesc'] = array('trDesc'); 
        $this->arrData['repeattypekey'] = array('selRepeatType');
        $this->arrData['warehousekey'] = array('selWarehouseKey');
        $this->arrData['repeatdate'] = array('trRepeatDate','date');
        $this->arrData['trdesc'] = array('trDesc');
        $this->arrData['statuskey'] = array('selStatus'); 
        
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 70));
        array_push($this->arrDataListAvailableColumn, array('code' => 'date','title' => 'date','dbfield' => 'repeatdate','default'=>true, 'width' => 150, 'align' =>'center', 'format' => 'date'));
        array_push($this->arrDataListAvailableColumn, array('code' => 'repeatEvery','title' => 'repeatEvery','dbfield' => 'repeatperiodname','default'=>true, 'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'supplier','title' => 'supplier','dbfield' => 'suppliername','default'=>true,'width' => 150));
        array_push($this->arrDataListAvailableColumn, array('code' => 'desc','title' => 'note','dbfield' => 'trdesc','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
         
        $this->actionMenu = array();  
        array_push($this->actionMenu,array('code' => 'runNow', 'name' => $this->lang['runNow'],  'icon' => 'run', 'url' => '../cron/routineCost'));
  
		
        $this->includeClassDependencies(array(
           'Car.class.php',
           'AP.class.php'
        ));
       
		
        $this->overwriteConfig();

   }
   
    function getQuery(){ 
        
        return '
			SELECT
                '.$this->tableName.'.*, 
                '.$this->tableRepeatPeriod.'.name as repeatperiodname,
                '.$this->tableSupplier.'.name as suppliername,
                '.$this->tableStatus.'.status as statusname
			FROM 
                '.$this->tableName.'
                    left join '.$this->tableSupplier.' on '.$this->tableName.'.supplierkey ='.$this->tableSupplier.'.pkey ,
                '.$this->tableRepeatPeriod.',
                '.$this->tableStatus.'
            WHERE 
                '.$this->tableName.'.repeattypekey = '.$this->tableRepeatPeriod.'.pkey and
                '.$this->tableName.'.statuskey = '.$this->tableStatus.'.pkey   

 		' .$this->criteria ;
		 
    }  
     
    function validateForm($arr,$pkey = ''){ 
                
		$arrayToJs = parent::validateForm($arr,$pkey);
        
        $arrDetailKey = $arr['hidDetailKey'];
        $arrAmount = $arr['amount']; 
        $arrDescription = $arr['description'];  
            
        for($i=0;$i<count($arrDetailKey);$i++) {   

            $arrAmount[$i] = $this->unFormatNumber($arrAmount[$i]);

            //jumlah tidak boleh nol harus lebih besar
            if($arrAmount[$i] <= 0) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['amount'][2]); 
            
            
            if( empty($arrDescription[$i])) 
                $this->addErrorList($arrayToJs,false,$this->errorMsg['routineCost'][2]); 
         

        }
          
		return $arrayToJs;
	 }
 
    function normalizeParameter($arrParam, $trim=false){
         
        // remove uncheck 
       
        $arrParam = parent::normalizeParameter($arrParam,true);  
        
    
        return $arrParam;
    }
    
    function getDetailWithRelatedInformation($pkey,$criteria=''){
        
        $sql = 'select
            '.$this->tableNameDetail.'.*
          from
            '.$this->tableNameDetail.'
          where  
            '. $this->tableNameDetail.'.refkey  = '.$this->oDbCon->paramString($pkey);

        $sql .= $criteria;
 
        return $this->oDbCon->doQuery($sql);

    } 
    
    
    function getChargeType($criteria=''){
        
         $sql = 'select * from '.$this->tableChargeType;
        
         if (!empty($criteria))  
            $sql .=  '' .$criteria; 

         return $this->oDbCon->doQuery($sql);

    }
    
    function runCron($arrPkey = array(), $systemTask = false){
         
        $car = new Car();
        $ap = new AP();  
        
        $criteria = '';
        
        if(!empty($arrPkey)) 
            $criteria .= ' and '.$this->tableName.'.pkey in ('.$this->oDbCon->paramString($arrPkey,',').')' ;
        
        $rs = $this->searchData($this->tableName.'.statuskey',1,true,$criteria);
        
        if(empty($rs)) return; 
         
         try{  

            if(!$this->oDbCon->startTrans())
                throw new Exception($this->errorMsg[100]); 

             $now = date('d / m / Y');
             foreach($rs as $headerRow){ 
                  
                $scheduleChecked = (!$systemTask) ? true : $this->checkCronJobSchedule($this,$headerRow['pkey']); 
                
                if (!$scheduleChecked) continue;
                 
                $rsDetail = $this->getDetailById($headerRow['pkey']); 
                     
                foreach($rsDetail as $row){ 

                        // kalo tipe mobil. kalikan dengan jumlah mobil
                        if($row['chargetype'] == 2){
                            $rsCar = $car->searchData($car->tableName.'.statuskey',1,true,' and supplierkey = '.$this->oDbCon->paramString($headerRow['supplierkey']));
                            $totalCar = (!empty($rsCar)) ? count($rsCar) : 0;
                            $row['amount'] *= $totalCar;
                        }
                            
                        if(empty($row['amount'])) continue;
                            
                        // add AP
                        $arrParam = array();	  
                        $arrParam['code'] = 'xxxxxx';
                        $arrParam['hidSupplierKey'] = $headerRow['supplierkey']; 
                        $arrParam['selWarehouse'] = $headerRow['warehousekey'];
                        $arrParam['selAPType'] = $row['aptype']; 
                        $arrParam['amount'] =  $row['amount'];  // blm dikalikan dengan jumlah mobil
                        $arrParam['trDate'] =  $now;   
                        $arrParam['dueDate'] = $now; 
                        $arrParam['trDesc'] = $row['description']; 
                        $arrParam['createdBy'] = 0;  
       
                        $arrayToJs = $ap->addData($arrParam);   
        
                        /*if (!$arrayToJs[0]['valid'])
                            throw new Exception('<strong>'.$rsHeader[0]['code'] . '</strong>. '.$this->errorMsg[201].' '.$arrayToJs[0]['message']);  */
                }  
            }

            $this->oDbCon->endTrans();   

        } catch(Exception $e){
            $this->oDbCon->rollback();  
        }	 
          
        
    }
    
}
?>