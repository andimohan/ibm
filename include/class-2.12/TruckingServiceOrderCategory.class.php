<?php

class TruckingServiceOrderCategory extends Category{
 
   function __construct(){
		
		parent::__construct();
       
		$this->tableName = 'trucking_service_order_category';  
        $this->tableNameDetail = 'trucking_service_order_category_detail';
		$this->securityObject = 'TruckingServiceOrderCategory'; 
        $this->tableTruckingJob = 'trucking_job';
		$this->uploadFolder = 'trucking_service-order-category/'; 
	  
        $this->arrLockedTable = array();
        $defaultFieldName = 'categorykey'; 
        array_push($this->arrLockedTable, array('table'=>'trucking_service_order_header','field'=>$defaultFieldName)); 
       
        $this->arrDeleteTable = array(); 
        array_push($this->arrDeleteTable, array('table'=>$this->tableNameDetail,'field' => 'refkey')); 
       
        $this->arrDataListAvailableColumn = array(); 
        array_push($this->arrDataListAvailableColumn, array('code' => 'code','title' => 'code','dbfield' => 'code','default'=>true, 'width' => 100));
        array_push($this->arrDataListAvailableColumn, array('code' => 'name','title' => 'name','dbfield' => 'name','default'=>true, 'width' => 200));
        array_push($this->arrDataListAvailableColumn, array('code' => 'status','title' => 'status','dbfield' => 'statusname','default'=>true, 'width' => 70));
       
	     
        $this->includeClassDependencies(array( 
                'TruckingJob.class.php', 
        ));  
	   
        $this->overwriteConfig();
   }
     
	
	 function validateForm($arr,$pkey = ''){

        $truckingJob = new TruckingJob();
         
		$arrayToJs = BaseClass::validateForm($arr,$pkey); 
		 
		$name = $arr['name'];  
         
		$orderlist = (!empty($arr['orderList'])) ? $this->unformatNumber($arr['orderList']) : 0;   
		    
		/*if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		} */
		
        $rsItem = $this->isValueExisted($pkey,'name',$name);	 
		if(empty($name)){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][1]);
		}else if(count($rsItem) <> 0){
			$this->addErrorList($arrayToJs,false,$this->errorMsg['category'][2]);
		}
         
        $arrDetailKeys = array();  
         
        if (isset($arr['hidJobTypeKey']) && !empty($arr['hidJobTypeKey'])){ 
            $arrJobType = $arr['hidJobTypeKey'];  
            for($i=0;$i<count($arrJobType);$i++) { 
                /*if (empty($arrJobType[$i]) ){ 
                    $this->addErrorList($arrayToJs,false, $this->errorMsg['jobType'][1]); 	
                } */

                 // cek ada detail double gk  
                if (in_array($arrJobType[$i],$arrDetailKeys)){  
                    $rsJob = $truckingJob->getDataRowById($arrJobType[$i]);
                    $this->addErrorList($arrayToJs,false, $rsJob[0]['name'].'. '.$this->errorMsg[215]); 	 
                }else{ 
                    array_push($arrDetailKeys, $arrJobType[$i]);
                } 
            }
        } 
		
		if (!empty($orderlist)){
			if (!is_numeric($orderlist)){
				$this->addErrorList($arrayToJs,false,$this->errorMsg['orderList'][2]);
			}
		}
		  
		return $arrayToJs;
	 }

    function afterUpdateData($arrParam, $action){
        //$this->updateOrder ($arrParam['orderList'],$arrParam['pkey']); 
        $this->updateLeaf();
	    $this->updateDetail ($arrParam);  
    }
    
    
    function updateDetail($arrParam){ 
        $pkey = $arrParam['pkey'];
        
        $sql = 'delete from '.$this->tableNameDetail.' where refkey = '. $this->oDbCon->paramString($pkey);
        $this->oDbCon->execute($sql);
        $truckingJob = new TruckingJob();
     
        $arrItemKey = array();
        $hasValue = false;
        
        if (isset($arrParam['hidJobTypeKey']) && !empty($arrParam['hidJobTypeKey'])){
            $arrItemKey = $arrParam['hidJobTypeKey'];  
            for ($i=0;$i<count($arrItemKey);$i++){
                if (!empty($arrItemKey[$i])){ 
                    $hasValue = true;
                    break;
                }
            } 
        }
		
        if (!$hasValue){
            $rsJob = $truckingJob->searchData($truckingJob->tableName.'.systemVariable',1,true);
            array_push($arrItemKey,$rsJob[0]['pkey']);  
        }         
          
     	for ($i=0;$i<count($arrItemKey);$i++){ 
            if (empty($arrItemKey[$i])) continue;   
            
			$sql = 'insert into '.$this->tableNameDetail.' ( 
						refkey,
                        jobtypekey 
					 ) values ( 
						'.$this->oDbCon->paramString($pkey).',
						'.$this->oDbCon->paramString($arrItemKey[$i]).' 
					)';	 
			$this->oDbCon->execute($sql);  
          
		}
					 
	}
    
    
   function getDetailWithRelatedInformation($pkey,$criteria=''){
   
       
	   $sql = 'select
	   			'.$this->tableNameDetail .'.*,  
                '.$this->tableTruckingJob.'.name as jobtypename 
                
			  from
			  	'.$this->tableNameDetail .',  
                '.$this->tableTruckingJob.'  
			  where 
			  	'.$this->tableNameDetail .'.jobtypekey = '.$this->tableTruckingJob.'.pkey and 
			  	refkey = '.$this->oDbCon->paramString($pkey)  ;
       
         
       
        $sql .= $criteria;
        
        $sql .= ' order by pkey asc';
        
		return $this->oDbCon->doQuery($sql);
	
   }
    
	function delete($id, $forceDelete = false,$reason = ''){ 
		
		$arrayToJs =  array();
		 
		try{			 
		
				$arrayToJs = $this->validateDelete($id);
				if (!empty($arrayToJs)) 
					return $arrayToJs;
					
					
				if (!$this->oDbCon->startTrans())
					throw new Exception($this->errorMsg[100]);
			
		 		$rs = $this->getDataRowById($id); 
			 
				$sql = 'delete from  '.$this->tableName.' where pkey = ' . $this->oDbCon->paramString($id);
				$this->oDbCon->execute($sql);  
            
                $this->deleteReference($id);
            
				$this->deleteAll($this->defaultDocUploadPath.$this->uploadFolder.$id); 
				$this->updateLeaf(); 
				
				$autoCode = new AutoCode(); 
				$rsAutoCode = $autoCode->searchData('code', $rs[0]['code'],true);
				$autoCode->delete($rsAutoCode[0]['pkey']);
				
                $this->setTransactionLog(DELETE_DATA,$id);	
            
				$this->oDbCon->endTrans();
										 
				$this->addErrorList($arrayToJs,true,$this->lang['dataHasBeenSuccessfullyUpdated']);    
			 
				
			}catch(Exception $e){
				$this->oDbCon->rollback();
				$this->addErrorList($arrayToJs,false, $e->getMessage()); 
		}			
			
		return $arrayToJs;	
	}
	  
    
}

?>